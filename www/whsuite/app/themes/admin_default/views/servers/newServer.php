<?php echo $view->fetch('elements/header.php'); ?>



    <div class="content-inner">
        <div class="container">
            <div class="row">

                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-server-add', array('id' => $group->id)))); ?>

                                <?php echo $forms->input('Server.name', $lang->get('name')); ?>

                                <?php echo $forms->input('Server.hostname', $lang->get('hostname')); ?>

                                <?php echo $forms->input('Server.main_ip', $lang->get('main_ip')); ?>

                                <?php echo $forms->input('Server.location', $lang->get('location')); ?>

                                <?php echo $forms->input('Server.username', $lang->get('username')); ?>

                                <?php echo $forms->input('Server.password', $lang->get('password')); ?>

                                <?php echo $forms->textarea('Server.api_key', $lang->get('api_key')); ?>

                                <?php echo $forms->checkbox('Server.ssl_connection', $lang->get('use_ssl_connection')); ?>

                                <?php echo $custom_fields; ?>

                                <?php echo $forms->input('Server.max_accounts', $lang->get('maximum_accounts')); ?>

                                <?php echo $forms->input('Server.priority', $lang->get('deployment_priority')); ?>
                                <span class="help-block"><?php echo $lang->get('deployment_priority_help_text'); ?></span>

                                <?php echo $forms->checkbox('Server.is_active', $lang->get('active')); ?>

                                <?php echo $forms->textarea('Server.notes', $lang->get('notes')); ?>

                                <?php echo $forms->input('Server.status_url', $lang->get('status_url')); ?>
                                <span class="help-block"><?php echo $lang->get('status_url_help_text'); ?></span>

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
