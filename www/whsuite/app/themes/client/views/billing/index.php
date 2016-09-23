<?php echo $view->fetch('elements/header.php'); ?>

<?php
    $process_cc = \App::get('configs')->get('settings.billing.enable_credit_card_payments');
    $store_cc = \App::get('configs')->get('settings.billing.store_credit_cards');
    $process_ach = \App::get('configs')->get('settings.billing.enable_ach_payments');
    $store_ach = \App::get('configs')->get('settings.billing.store_ach');
?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default panel-tabs panel-newsbox">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#accounts" data-toggle="tab"><?php echo $lang->get('payment_accounts'); ?></a></li>
                    <li><a href="#addfunds" data-toggle="tab"><?php echo $lang->get('add_funds'); ?></a></li>
                    <?php if ($process_cc && $store_cc): ?>
                        <li><a href="#addcc" data-toggle="tab"><?php echo $lang->get('add_cc'); ?></a></li>
                    <?php endif; ?>
                    <?php if ($process_ach && $store_ach): ?>
                        <li><a href="#addach" data-toggle="tab"><?php echo $lang->get('add_ach'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="tab-content">
                <div class="panel-content panel-table tab-pane active" id="accounts">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $lang->get('name'); ?></th>
                                <th><?php echo $lang->get('last4'); ?></th>
                                <th><?php echo $lang->get('type'); ?></th>
                                <th><?php echo $lang->get('currency'); ?></th>
                                <th class="text-center"><?php echo $lang->get('status'); ?></th>
                                <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($payment_accounts) > 0): ?>
                                <?php foreach($payment_accounts as $account): ?>

                                    <tr>
                                        <td><?php echo $account->first_name . ' ' . $account->last_name; ?></td>
                                        <td><?php echo $account->account_last4; ?></td>
                                        <td>
                                            <?php
                                                if (in_array($account->account_type, array('savings', 'checking'))):
                                                    echo $lang->get('ach_account') . ' (' . $lang->get($account->account_type) . ')';
                                                else:
                                                    echo $lang->get('credit_card') . ' (' . $lang->get($account->account_type) . ')';
                                                endif;
                                            ?>
                                        </td>
                                        <td><?php echo $account->Currency->code; ?></td>
                                        <td class="text-center">
                                            <?php if($account->is_active == '1'): ?>
                                                <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-danger"><?php echo $lang->get('inactive'); ?></span>
                                            <?php endif; ?>

                                            <?php if($account->is_default == '1'): ?>
                                                <?php if (in_array($account->account_type, array('savings', 'checking'))): ?>
                                                    <span class="label label-info"><?php echo $lang->get('default_ach'); ?></span>
                                                <?php else: ?>
                                                    <span class="label label-info"><?php echo $lang->get('default_cc'); ?></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <?php if (in_array($account->account_type, array('savings', 'checking'))): ?>
                                                <a href="<?php echo $router->generate('client-manage-ach', array('id' => $account->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('manage'); ?></a>
                                            <?php else: ?>
                                                <a href="<?php echo $router->generate('client-manage-cc', array('id' => $account->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('manage'); ?></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel-content tab-pane" id="addfunds">
                    <?php
                        echo $forms->open(array(
                            'action' => $router->generate('client-account-credit'),
                            'method' => 'post',
                            'class' => 'form-horizontal form-load'
                        ));

                            echo $forms->select(
                                'Invoice.currency_id',
                                $lang->get('currency'),
                                array(
                                    'options' => Currency::formattedList('id', 'code')
                                )
                            );

                            echo $forms->input(
                                'Invoice.amount',
                                $lang->get('amount')
                            );
                    ?>
                            <div class="form-actions">
                                <?php echo $forms->submit('submit', $lang->get('create_invoice')); ?>
                            </div>
                    <?php echo $forms->close(); ?>
                </div>

                <?php if ($process_cc && $store_cc): ?>
                    <div class="panel-content tab-pane" id="addcc">
                        <?php echo $forms->open(array('action' => $router->generate('client-add-cc'), 'method' => 'post')); ?>
                            <?php echo $forms->input('Cc.first_name', $lang->get('first_name'), array('value' => $client->first_name)); ?>
                            <?php echo $forms->input('Cc.last_name', $lang->get('last_name'), array('value' => $client->last_name)); ?>
                            <?php echo $forms->input('Cc.company', $lang->get('company'), array('value' => $client->company)); ?>
                            <?php echo $forms->input('Cc.email', $lang->get('email'), array('value' => $client->email)); ?>

                            <?php echo $forms->input('Cc.address1', $lang->get('address1'), array('value' => $client->address1)); ?>
                            <?php echo $forms->input('Cc.address2', $lang->get('address2'), array('value' => $client->address2)); ?>
                            <?php echo $forms->input('Cc.city', $lang->get('city'), array('value' => $client->city)); ?>
                            <?php echo $forms->input('Cc.state', $lang->get('state'), array('value' => $client->state)); ?>
                            <?php echo $forms->input('Cc.postcode', $lang->get('postcode'), array('value' => $client->postcode)); ?>
                            <?php echo $forms->select('Cc.country', $lang->get('country'), array('options' => $country_list, 'value' => $client->country)); ?>

                            <?php echo $forms->checkbox('Cc.is_active', $lang->get('active')); ?>
                            <span class="help-block"><?php echo $lang->get('card_can_be_disabled'); ?></span>

                            <?php echo $forms->checkbox('Cc.is_default', $lang->get('default_cc')); ?>
                            <span class="help-block"><?php echo $lang->get('card_can_be_default'); ?></span>
                            <hr>
                            <?php echo $forms->select('Cc.currency_id', $lang->get('currency'), array('options' => $currency_list)); ?>
                            <?php echo $forms->input('Cc.account_number', $lang->get('card_number')); ?>
                            <?php echo $forms->input('Cc.account_expiry', $lang->get('expiry_date'), array('placeholder' => 'MMYY')); ?>

                            <div class="form-actions">
                                <?php echo $forms->submit('submit', $lang->get('add_cc')); ?>
                            </div>
                        <?php echo $forms->close(); ?>
                    </div>
                <?php endif; ?>

                <?php if ($process_ach && $store_ach): ?>
                    <div class="panel-content tab-pane" id="addach">
                        <?php echo $forms->open(array('action' => $router->generate('client-add-ach'), 'method' => 'post')); ?>
                            <?php echo $forms->input('Ach.first_name', $lang->get('first_name'), array('value' => $client->first_name)); ?>
                            <?php echo $forms->input('Ach.last_name', $lang->get('last_name'), array('value' => $client->last_name)); ?>
                            <?php echo $forms->input('Ach.company', $lang->get('company'), array('value' => $client->company)); ?>
                            <?php echo $forms->input('Ach.email', $lang->get('email'), array('value' => $client->email)); ?>

                            <?php echo $forms->input('Ach.address1', $lang->get('address1'), array('value' => $client->address1)); ?>
                            <?php echo $forms->input('Ach.address2', $lang->get('address2'), array('value' => $client->address2)); ?>
                            <?php echo $forms->input('Ach.city', $lang->get('city'), array('value' => $client->city)); ?>
                            <?php echo $forms->input('Ach.state', $lang->get('state'), array('value' => $client->state)); ?>
                            <?php echo $forms->input('Ach.postcode', $lang->get('postcode'), array('value' => $client->postcode)); ?>
                            <?php echo $forms->select('Ach.country', $lang->get('country'), array('options' => $country_list, 'value' => $client->country)); ?>

                            <?php echo $forms->checkbox('Ach.is_active', $lang->get('active')); ?>
                            <span class="help-block"><?php echo $lang->get('ach_can_be_disabled'); ?></span>

                            <?php echo $forms->checkbox('Ach.is_default', $lang->get('default_ach')); ?>
                            <span class="help-block"><?php echo $lang->get('ach_can_be_default'); ?></span>
                            <hr>
                            <?php echo $forms->select('Ach.currency_id', $lang->get('currency'), array('options' => $currency_list)); ?>
                            <?php echo $forms->input('Ach.account_number', $lang->get('account_number')); ?>
                            <?php echo $forms->input('Ach.account_routing_number', $lang->get('routing_number')); ?>
                            <?php echo $forms->select('Ach.account_type', $lang->get('account_type'), array('options' => $ach_account_types)); ?>

                            <div class="form-actions">
                                <?php echo $forms->submit('submit', $lang->get('add_ach')); ?>
                            </div>
                        <?php echo $forms->close(); ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
