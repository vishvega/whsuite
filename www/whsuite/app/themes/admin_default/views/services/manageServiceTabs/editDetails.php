<?php
    echo $forms->open(
        array(
            'action' => $router->generate('admin-service-edit-details',
            array(
                'id' => $client->id,
                'service_id' => $purchase->id)
            ),
            'role' => 'form'
        )
    );
?>
    <fieldset>
        <legend><?php echo $lang->get('service_details'); ?></legend>
        <?php echo $forms->select('Purchase.currency_id', $lang->get('currency'), array('options' => $currencies)); ?>
        <?php echo $forms->select('Purchase.billing_period_id', $lang->get('billing_period'), array('options' => $billing_periods)); ?>
        <?php echo $forms->input('Purchase.first_payment', $lang->get('first_payment'), array('value' => App::get('money')->format($purchase->first_payment, $currency->code, true))); ?>
        <?php echo $forms->input('Purchase.recurring_payment', $lang->get('recurring_payment'), array('value' => App::get('money')->format($purchase->recurring_payment, $currency->code, true))); ?>
        <?php echo $forms->input('Purchase.next_renewal', $lang->get('next_due_date')); ?>
        <?php echo $forms->input('Purchase.next_invoice', $lang->get('next_invoice')); ?>
        <?php echo $forms->select('Purchase.promotion_id', $lang->get('promotion_code'), array('options' => $promotions)); ?>
        <?php echo $forms->select('Purchase.status', $lang->get('status'), array('options' => $service_statuses)); ?>
        <?php echo $forms->checkbox('Purchase.disable_autosuspend', $lang->get('disable_autosuspend')); ?>
        <?php echo $forms->textarea('Purchase.suspend_notice', $lang->get('suspension_notice')); ?>
        <?php echo $forms->select('Purchase.gateway_id', $lang->get('payment_gateway'), array('options' => $gateways_list)); ?>
        <?php echo $forms->textarea('Purchase.payment_subscription', $lang->get('gateway_subscription_data')); ?>
        <?php echo $forms->textarea('Purchase.notes', $lang->get('admin_notes')); ?>
    </fieldset>

    <?php if($type == 'hosting'): ?>
        <?php echo $view->display('services/manageServiceTabs/editDetailsHosting.php'); ?>
    <?php elseif($type == 'domain'): ?>
        <?php echo $view->display('services/manageServiceTabs/editDetailsDomain.php'); ?>
    <?php endif; ?>

    <div class="form-actions">
        <?php echo $forms->submit('submit', $lang->get('save')); ?>
    </div>
<?php echo $forms->close(); ?>
