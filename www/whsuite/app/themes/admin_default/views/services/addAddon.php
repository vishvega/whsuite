<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-service-add-addon-save', array('id' => $client->id, 'service_id' => $purchase->id)))); ?>
                                <?php echo $forms->hidden('addon_id', array('value' => $addon->id)); ?>
                                <?php echo $forms->input('first_payment', $lang->get('first_payment'), array('value' => $first_payment)); ?>
                                <?php echo $forms->input('recurring_payment', $lang->get('recurring_payment'), array('value' => $recurring_payment)); ?>
                                <?php echo $forms->checkbox('is_active', $lang->get('activate_addon')); ?>
                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('add_addon')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php echo $view->fetch('elements/footer.php'); ?>
