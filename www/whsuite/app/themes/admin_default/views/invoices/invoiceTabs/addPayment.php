<?php echo $forms->open(array('method' => 'post', 'action' => $router->generate('admin-client-invoice-add-payment', array('id' => $client->id, 'invoice_id' => $invoice->id)))); ?>
    <?php echo $forms->input('amount', $lang->get('amount'), array('value' => App::get('money')->format(($invoice->total - $invoice->total_paid), $currency->code, true))); ?>
    <?php echo $forms->textarea('description', $lang->get('description'), array('rows' => '2')); ?>
    <?php echo $forms->select('gateway', $lang->get('gateway'), array('options' => $gateways)); ?>
    <div class="form-actions">
        <?php echo $forms->submit('submit', $lang->get('save')); ?>
    </div>
<?php echo $forms->close(); ?>
