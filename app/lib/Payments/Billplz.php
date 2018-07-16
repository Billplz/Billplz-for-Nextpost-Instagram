<?php
namespace Payments;

use Billplz\API;
use Billplz\Connect;

/**
 * Billplz Payment Gateway
 */
class Billplz extends AbstractGateway
{
    public $description;
    private $x_signature;
    private $api_key;
    private $collection_id;

    public function __construct()
    {
        $integrations = \Controller::model("GeneralData", "integrations");

        $this->api_key = $integrations->get("data.billplz.api_key");
        $this->collection_id = $integrations->get("data.billplz.cid");
        $this->x_signature = $integrations->get("data.billplz.x_signature");
    }

    /**
     * Place Order
     *
     * Generate payment page url here and return it
     * @return string URL of the payment page
     */
    public function placeOrder($params = [])
    {
        $Order = $this->getOrder();
        if (!$Order) {
            throw new \Exception('Set order before calling AbstractGateway::placeOrder()');
        }

        $currency_setting = $Order->get("currency");
        if ($currency_setting !== 'MYR') {
            throw new \Exception('Billplz couldn\'t continue since currency is not set to MYR');
        }
        
        $User = $this->getUser();
        if (!$User->isAvailable() || !$User->get("is_active")) {
            throw new \Exception('User is not available or active');
        }
        
        if ($Order->get("status") != "payment_processing") {
            throw new \Exception('Order status must be payment_processing to place it');
        }

        $connnect = (new Connect($this->api_key))->detectMode();
        $billplz = new API($connnect);

        $parameter = array(
            'collection_id' => $this->collection_id,
            'email'=> $User->get('email'),
            'mobile'=>'0141234567',
            'name'=> $User->get('firstname').' '. $User->get('lastname'),
            'amount'=>strval($Order->get("total") * 100),
            'callback_url'=> APPURL.'/webhooks/payments/billplz',
            'description'=> mb_substr($Order->get("data.package.title") . " " . $Order->get("data.plan") == "annual" ? __("Annual Plan") : __("Monthly Plan"), 0, 199)
        );
        $optional = array(
            'redirect_url' => APPURL."/checkout/".$Order->get("id").".".sha1($Order->get("id").NP_SALT),
            'reference_1_label' => 'Order ID',
            'reference_1' => $Order->get("id")
        );

        list($rheader, $rbody) = $billplz->toArray($billplz->createBill($parameter, $optional));

        if ($rheader !== 200) {
            $Order->delete();
            $url = APPURL."/checkout/error";
        } else {
            $url = $rbody['url'];
        }

        return $url;
    }

    /**
     * Handle Billplz Callback
     */
    public function webhook()
    {
        $this->callback();
    }

    /**
     * Handle Billplz Redirect
     * @return boolean [description]
     */
    public function callback($params = [])
    {
    
        $data = Connect::getXSignature($this->x_signature);
        
        $connnect = (new Connect($this->api_key))->detectMode();
        $billplz = new API($connnect);
        list($rheader, $rbody) = $billplz->toArray($billplz->getBill($data['id']));

        $paymentId = $rbody['reference_1'];

        /* We didn't use this because it doesn't valid for callback */
        /* $Order = $this->getOrder(); */
        $Order = \Controller::model("Order", $paymentId);
        
        if (!$Order) {
            throw new \Exception('Set order before calling AbstractGateway::placeOrder()');
        }


        if (!$Order->get("payment_id")) {
            // If payment_id is not empty
            // then it means that, order is already completed

            if ($Order->get("status") == "payment_processing") {
                $Order->finishProcessing();

                // Updating order...
                $Order->set("status", "paid")
                      ->set("payment_id", $rbody['id'])
                      ->set("paid", number_format($rbody['amount']/100, 2, ".", ""))
                      ->update();

                try {
                    // Send notification emails to admins
                    \Email::sendNotification("new-payment", ["order" => $Order]);
                } catch (\Exception $e) {
                    // Failed to send notification email to admins
                    // Do nothing here, it's not critical error
                }
            }
        }
        
        if ($data['type'] === 'callback') {
            exit('Callback Action Done');
        }
        
        return true;
    }
}
