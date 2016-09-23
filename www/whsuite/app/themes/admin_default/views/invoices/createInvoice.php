<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('method' => 'post', 'action' => $router->generate('admin-client-new-invoice', array('id' => $client->id)))); ?>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <?php echo $forms->input('Invoice[date_due]', $lang->get('date_due')); ?>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php echo $forms->select('Invoice[currency_id]', $lang->get('currency'), array('options' => $currencies, 'value' => $client->currency_id)); ?>
                                    </div>
                                </div>

                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>
