# Billplz for Nextpost Instagram

Accept payment using Billplz by using this plugin.

## System Requirements
* PHP Version **7.0** or later
* Build with **Nextpost Instagram** version **3.0.6**
* URL: https://codecanyon.net/item/nextpost-auto-post-schedule-manage-your-instagram-multi-accounts-php-script/19456996

## Installation

-  Copy all files to installation directory. 
-  Set pwd to **app** folder and execute:
   ```bash
   composer require billplz/billplz-api
   ```
-  Edit file: __*app/views/fragments/settings/menu.fragment.php*__. Add this line after stripe link:
    ```html
    <li class="<?= $page == "billplz" ? "active" : "" ?>">
        <a href="<?= APPURL."/settings/billplz" ?>"><?= __("Billplz Integration") ?></a>
    </li>
   ```
-  Edit file: __*app/controllers/SettingsController.php*__. Add this method inside **class SettingsController**:
   ```php
   private function saveBillplz()
   {
        $Integrations = $this->getVariable("Integrations");

        $secret = Input::post("x-signature");
        $cid = Input::post("cid");
        $api_key = Input::post("api-key");

        $Integrations->set("data.billplz.api_key", $api_key)
                     ->set("data.billplz.x_signature", $secret)
                     ->set("data.billplz.cid", $cid)
                     ->save();

        $this->resp->result = 1;
        $this->resp->msg = __("Changes saved!");
        $this->jsonecho();
        return $this;
    }
   ```
-  Edit file: __*index.php*__. Find **$settings_pages** array and add **"billplz"** string in that array. Example:
   ```php
   $settings_pages = [
      "site", "logotype", "other",
      "google-analytics", "google-drive", "dropbox", "onedrive", "paypal", "stripe", 
      "facebook", "proxy", "billplz",

      "notifications", "smtp"
    ];
   ```
   
   Note: If you are using Nextpost Instagram version 4.1.x, the **$settings_pages** can be located on **app\inc\routes.inc.php**
   
-  Edit file: __*app/views/fragments/renew.fragment.php*__. Before the block **"data.paypal.client_id"**, add this block:
   ```php
   <?php 
   if ($Integrations->get("data.billplz.api_key") 
   && $Integrations->get("data.billplz.x_signature")): 
   ?>
   <div class="option-group-item">
        <label>
            <input class="custom-radio" name="payment-gateway" type="radio" value="billplz" data-recurring="false">
            <div>
                <div class="text-c">
                    <img style="height: 100px;" src="<?= APPURL."/assets/img/cc/billplz.jpg" ?>" alt="Visa">
                </div>
            </div>
        </label>
    </div>
    <?php endif ?>
   ```
-  Edit file: __*app/helpers/plugin.helper.php*__. Add this elemen inside **$gateways** array:
   ```php
   "billplz" => "\Payments\Billplz",
   ```
-  Edit file: __*assets/js/core.js*__. After else if block for stripe, add this block:
    ```js
    else if (data.payment_gateway == "billplz") {
        placeBillplzOrder();
    }
   ```
-  Edit file: __*assets/js/core.js*__. In the NextPost.Renew anonymous function, add this block:
   ```js
    var _placeBillplzOrder = function() {
        data.action = "pay";
        $("body").addClass("onprogress");
        $.ajax({
            url: $form.data("url"),
            type: 'POST',
            dataType: 'jsonp',
            data: data,
            error: function() {
                NextPost.Alert();
                $("body").removeClass("onprogress");
            },

            success: function(resp) {
                if (resp.result == 1) {
                    window.location.href = resp.url;
                } else {
                    NextPost.Alert({
                        content: resp.msg
                    });

                    $("body").removeClass("onprogress");
                }
            }
        });
    }            
   ```

## Configuration

1. **Settings** >> **Billplz Integration**
2. Set **API Key**, **X Signature Key** & Collection ID
3. Save Changes

## Troubleshooting

* Please make sure you have enabled X Signature Key properly on your [Billplz Account Settings](https://www.billplz.com/enterprise/setting)

## Other

Facebook: [Billplz Dev Jam](https://www.facebook.com/groups/billplzdevjam/)
