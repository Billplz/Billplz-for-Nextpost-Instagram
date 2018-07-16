            <form class="js-ajax-form"
                  action="<?= APPURL . "/settings/" . $page ?>"
                  method="POST"
                  id="billplz-form">
                <input type="hidden" name="action" value="save">

                <div class="section-header clearfix">
                    <h2 class="section-title"><?= __("Billplz Integration") ?></h2>
                    <div class="section-actions clearfix hide-on-large-only">
                        <a class="mdi mdi-menu-down icon js-settings-menu" href="javascript:void(0)"></a>
                    </div>
                </div>

                <div class="section-content">
                    <div class="clearfix">
                        <div class="col s12 m6 l5">
                            <div class="form-result"></div>

                            <div class="mb-40">
                                <label class="form-label">
                                    <?= __("API Secret Key") ?>
                                    <span class="compulsory-field-indicator">*</span>
                                </label>

                                <input class="input"
                                       name="api-key"
                                       type="text"
                                       value="<?= htmlchars($Integrations->get("data.billplz.api_key")) ?>"
                                       maxlength="100">
                            </div>

                            <div class="mb-40">
                                <label class="form-label">
                                    <?= __("X Signature Key") ?>
                                    <span class="compulsory-field-indicator">*</span>
                                </label>

                                <input class="input"
                                       name="x-signature"
                                       type="text"
                                       value="<?= htmlchars($Integrations->get("data.billplz.x_signature")) ?>"
                                       maxlength="100">
                            </div>

                            <div class="mb-40">
                                <label class="form-label">
                                    <?= __("Collection ID") ?>
                                </label>

                                <input class="input"
                                       name="cid"
                                       type="text"
                                       value="<?= htmlchars($Integrations->get("data.billplz.cid")) ?>"
                                       maxlength="100">
                            </div>
                        </div>

                        <div class="col s12 m6 m-last l5 offset-l1 mb-40 js-notes">
                            <label class="form-label mb-10">Notes</label>

                            <p>
                                You should follow these steps to retrieve:                            </p>

                            <ul class="field-tips">
                                <li class="mb-15">
                                    Get Your API Secret Key<br>
                                    <a href="https://www.billplz.com/enterprise/setting" target="_blank">https://www.billplz.com/enterprise/setting</a>
                                </li>

                                <li class="mb-15">
                                    Get your X Signature Key. <br>
                                    Login to Billplz >> Settings >> Enable X Signature. <br>
                                    <a href="https://www.billplz.com/enterprise/setting" target="_blank">https://www.billplz.com/enterprise/setting</a>
                                </li>

                                <li class="mb-15">
                                    Create a Collection Id <br>
                                    Login to Billplz >> Billing >> Create Collection. <br>
                                    <a href="https://www.billplz.com/enterprise/billing" target="_blank">https://www.billplz.com/enterprise/billing</a>
                                </li>
                                
                           </ul>
                        </div>                       
                    </div>

                    <div class="clearfix">
                      <div class="col s12 m6 l5">
                          <input class="fluid button" type="submit" value="<?= __("Save") ?>">
                      </div>
                    </div>
                </div>
            </form>
