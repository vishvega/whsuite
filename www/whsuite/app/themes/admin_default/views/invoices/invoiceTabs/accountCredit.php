<?php echo $forms->open(array('method' => 'post', 'action' => $router->generate('admin-client-invoice-apply-credit', array('id' => $client->id, 'invoice_id' => $invoice->id)))); ?>
    <?php echo $forms->input('current_credit', $lang->get('available_credit'), array('value' => $client_credit, 'disabled' => 'disabled')); ?>
    <?php echo $forms->input('amount', $lang->get('amount_to_apply'), array('value' => App::get('money')->format('0', $currency->code, true))); ?>
    <div class="form-actions">
        <?php echo $forms->submit('submit', $lang->get('save')); ?>
    </div>
<?php echo $forms->close(); ?>
