<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-service-manage-addon', array('id' => $client->id, 'service_id' => $purchase->id, 'addon_purchase_id' => $addon_purchase->id)))); ?>
                                <?php echo $forms->input('first_payment', $lang->get('first_payment'), array('value' => $addon_purchase->first_payment)); ?>
                                <?php echo $forms->input('recurring_payment', $lang->get('recurring_payment'), array('value' => $addon_purchase->recurring_payment)); ?>
                                <?php
                                if($addon_purchase->is_active == '1'):
                                    $checked = 'checked';
                                else:
                                    $checked = '';
                                endif;
                                ?>
                                <?php echo $forms->checkbox('is_active', $lang->get('active'), array('value' => $addon_purchase->is_active, 'checked' => $checked)); ?>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                    <a href="<?php echo $router->generate('admin-service-delete-addon', array('id' => $client->id, 'service_id' => $purchase->id, 'addon_purchase_id' => $addon_purchase->id)); ?>" onclick="return confirm('<?php echo $lang->get('popup_confirm'); ?>');" class="btn btn-danger pull-right">
                                        <?php echo $lang->get('delete_product_addon'); ?>
                                    </a>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php'); ?>
