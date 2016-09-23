<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">

                <div class="well well-sm text-center">
                    <h4><?php echo $lang->get('total_due'); ?>: <?php echo App::get('money')->format($total_due, $invoice->currency_id, false, true); ?></h4>
                </div>

                <?php
                echo $forms->open(array(
                    'action' => $router->generate('client-invoice-pay', array('id' => $invoice->id)),
                    'method' => 'post',
                    'class' => 'form-horizontal form-load'
                ));
                ?>

                    <div class="form-group">
                        <label for="account"><?php echo $lang->get('payment_method'); ?></label>
                        <?php
                            $route_check_url = $router->generate(
                                'client-ajax-pay-button-check',
                                array(
                                    'gateway' => 'GATEWAY',
                                    'invoice_id' => 'INVOICEID'
                                )
                            );

                            $default_btn_url = $router->generate(
                                'client-ajax-pay-button-default'
                            );
                        ?>
                        <select
                            name="account"
                            id="account"
                            class="form-control"
                            data-route-check-url="<?php echo $route_check_url; ?>"
                            data-default-button-url="<?php echo $default_btn_url; ?>"
                            data-invoice-id="<?php echo $invoice->id; ?>"
                        >
                            <option disabled selected="selected"><?php echo $lang->get('select'); ?></option>

                            <?php if(!empty($gateways)): ?>
                                <optgroup label="<?php echo $lang->get('gateways'); ?>">
                                    <?php foreach($gateways as $gateway): ?>
                                        <option value="gateway_<?php echo $gateway->slug; ?>"><?php echo $gateway->name; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>

                            <?php if($cc_enabled): ?>
                                <option value="creditcard"><?php echo $lang->get('credit_card'); ?></option>
                            <?php endif ?>

                            <?php if($ach_enabled): ?>
                                <option value="ach"><?php echo $lang->get('automated_clearing_house'); ?></option>
                            <?php endif; ?>

                            <?php if(!empty($payment_accounts_cc_select_list)): ?>
                                <optgroup label="<?php echo $lang->get('credit_cards'); ?>">
                                    <?php foreach($payment_accounts_cc_select_list as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>

                            <?php if(!empty($payment_accounts_ach_select_list)): ?>
                                <optgroup label="<?php echo $lang->get('ach_accounts'); ?>">
                                    <?php foreach($payment_accounts_ach_select_list as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>

                            <?php if (isset($accountCreditAvailable) && $accountCreditAvailable === true): ?>
                                <option value="credit"><?php echo $lang->get('account_credit'); ?></option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div id="creditcard_dropdown" style="display:none;">
                        <?php echo $forms->input('Cc.first_name', $lang->get('first_name'), array('value' => $client->first_name)); ?>
                        <?php echo $forms->input('Cc.last_name', $lang->get('last_name'), array('value' => $client->last_name)); ?>
                        <?php echo $forms->input('Cc.company', $lang->get('company'), array('value' => $client->company)); ?>
                        <?php echo $forms->input('Cc.email', $lang->get('email'), array('value' => $client->email)); ?>
                        <?php echo $forms->select('Cc.customer_type', $lang->get('customer_type'), array('options' => ClientCc::$customer_types)); ?>

                        <?php echo $forms->input('Cc.address1', $lang->get('address1'), array('value' => $client->address1)); ?>
                        <?php echo $forms->input('Cc.address2', $lang->get('address2'), array('value' => $client->address2)); ?>
                        <?php echo $forms->input('Cc.city', $lang->get('city'), array('value' => $client->city)); ?>
                        <?php echo $forms->input('Cc.state', $lang->get('state'), array('value' => $client->state)); ?>
                        <?php echo $forms->input('Cc.postcode', $lang->get('postcode'), array('value' => $client->postcode)); ?>
                        <?php echo $forms->select('Cc.country', $lang->get('country'), array('options' => $country_list, 'value' => $client->country)); ?>
                        <hr>
                        <?php echo $forms->input('Cc.account_number', $lang->get('account_number')); ?>
                        <?php echo $forms->input('Cc.account_expiry', $lang->get('expiry_date'), array('placeholder' => 'MMYY')); ?>
                    </div>

                    <div id="ach_dropdown" style="display:none;">
                        <?php echo $forms->input('Ach.first_name', $lang->get('first_name'), array('value' => $client->first_name)); ?>
                        <?php echo $forms->input('Ach.last_name', $lang->get('last_name'), array('value' => $client->last_name)); ?>
                        <?php echo $forms->input('Ach.company', $lang->get('company'), array('value' => $client->company)); ?>
                        <?php echo $forms->input('Ach.email', $lang->get('email'), array('value' => $client->email)); ?>
                        <?php echo $forms->select('Ach.customer_type', $lang->get('customer_type'), array('options' => ClientAch::$customer_types)); ?>

                        <?php echo $forms->input('Ach.address1', $lang->get('address1'), array('value' => $client->address1)); ?>
                        <?php echo $forms->input('Ach.address2', $lang->get('address2'), array('value' => $client->address2)); ?>
                        <?php echo $forms->input('Ach.city', $lang->get('city'), array('value' => $client->city)); ?>
                        <?php echo $forms->input('Ach.state', $lang->get('state'), array('value' => $client->state)); ?>
                        <?php echo $forms->input('Ach.postcode', $lang->get('postcode'), array('value' => $client->postcode)); ?>
                        <?php echo $forms->select('Ach.country', $lang->get('country'), array('options' => $country_list, 'value' => $client->country)); ?>
                        <hr>
                        <?php echo $forms->input('Ach.account_number', $lang->get('account_number')); ?>
                        <?php echo $forms->input('Ach.account_routing_number', $lang->get('routing_number')); ?>
                        <?php echo $forms->select('Ach.account_type', $lang->get('account_type'), array('options' => $ach_account_types)); ?>
                    </div>

                <div class="form-actions">
                    <div id="pay-invoice-submit">
                        <?php echo $view->fetch('elements/pay-invoice-btn.php'); ?>
                    </div>
                </div>
                <?php echo $forms->close(); ?>
            </div>
        </div>
    </div>
</div>

<?php echo $assets->script('pay-invoice.js'); ?>


<?php echo $view->fetch('elements/footer.php'); ?>
