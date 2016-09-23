<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-servergroup-add'))); ?>

                                <?php echo $forms->input('Group.name', $lang->get('name')); ?>

                                <?php echo $forms->textarea('Group.description', $lang->get('description')); ?>

                                <?php echo $forms->checkbox('Group.autofill', $lang->get('auto_fill_servers')); ?>
                                <span class="help-block"><?php echo $lang->get('auto_fill_servers_help_text'); ?></span>

                                <?php echo $forms->select('Group.server_module_id', $lang->get('server_module'), array('options' => $server_modules)); ?>
                                <span class="help-block"><?php echo $lang->get('server_module_help_text'); ?></span>

                                <?php echo $group->customFields(false); ?>

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