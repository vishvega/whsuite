<?php echo $forms->open(array('method' => 'post', 'action' => $router->generate('admin-client-invoice-update-settings', array('id' => $client->id, 'invoice_id' => $invoice->id)))); ?>
    <?php echo $forms->input('invoice.date_due', $lang->get('date_due')); ?>
    <?php echo $forms->input('invoice.level1_rate', $lang->get('level_1_tax').' (%)'); ?>
    <?php echo $forms->input('invoice.level2_rate', $lang->get('level_2_tax').' (%)'); ?>
    <?php echo $forms->textarea('invoice.notes', $lang->get('notes')); ?>
    <?php echo $forms->select('invoice.status', $lang->get('status'), array('options' => Invoice::formattedStatusList(), 'value'=> $invoice->status)); ?>

    <div class="form-actions">
        <?php echo $forms->submit('submit', $lang->get('save')); ?>
    </div>
<?php echo $forms->close(); ?>
