<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('custom_field_group_details'); ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-custom-fields-new-group'))); ?>

                                <?php echo $forms->input('Group.slug', $lang->get('slug')); ?>
                                <?php echo $forms->input('Group.name', $lang->get('name')); ?>
                                <?php echo $forms->select('Group.addon_id', $lang->get('addon'), array('options' => $addons)); ?>
                                <?php echo $forms->checkbox('Group.is_editable', $lang->get('fields_editable_by_staff')); ?>
                                <?php echo $forms->checkbox('Group.is_active', $lang->get('active')); ?>

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
<?php echo $view->fetch('elements/footer.php');
