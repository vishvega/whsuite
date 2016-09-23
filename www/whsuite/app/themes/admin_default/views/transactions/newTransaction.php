<?php echo $view->fetch('elements/header.php'); ?>
    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <?php echo $view->fetch('clients/profileSidebar/info.php'); ?>
                </div>
                <div class="col-lg-8">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('new_transaction'); ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('method' => 'post', 'action' => $router->generate('admin-client-new-transaction', array('id' => $client->id)))); ?>
                                <?php echo $forms->select('currency_id', $lang->get('currency'), array('options' => $currencies)); ?>
                                <?php echo $forms->select('gateway_id', $lang->get('gateway'), array('options' => $gateways)); ?>
                                <?php echo $forms->select('type', $lang->get('transaction_type'), array('options' => Transaction::typesList())); ?>
                                <p class="help-block"><?php echo $lang->get('transaction_types_description'); ?></p>
                                <?php echo $forms->input('amount', $lang->get('amount'), array('value' => '0')); ?>
                                <?php echo $forms->textarea('description', $lang->get('description')); ?>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php'); ?>
