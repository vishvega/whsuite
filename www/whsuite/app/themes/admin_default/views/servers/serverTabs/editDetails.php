<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('edit_details'); ?></div>
            <div class="panel-content">
                <?php echo $forms->open(array('action' => $router->generate('admin-server-manage', array('id' => $group->id, 'server_id' => $server->id)))); ?>

                    <?php echo $forms->input('Server.name', $lang->get('name')); ?>

                    <?php echo $forms->input('Server.hostname', $lang->get('hostname')); ?>

                    <?php echo $forms->input('Server.main_ip', $lang->get('main_ip')); ?>

                    <?php echo $forms->input('Server.location', $lang->get('location')); ?>

                    <?php echo $forms->input('Server.username', $lang->get('username'), array('value' => App::get('security')->decrypt($server->username))); ?>

                    <?php echo $forms->input('Server.password', $lang->get('change_password')); ?>
                    <span class="help-block"><?php echo $lang->get('leave_blank_to_keep_existing_value'); ?></span>

                    <?php echo $forms->textarea('Server.api_key', $lang->get('change_api_key')); ?>
                    <span class="help-block"><?php echo $lang->get('leave_blank_to_keep_existing_value'); ?></span>

                    <?php echo $forms->checkbox('Server.ssl_connection', $lang->get('use_ssl_connection')); ?>

                    <?php
                    if(isset($custom_fields)):
                        echo $custom_fields;
                    endif;
                    ?>

                    <?php echo $forms->input('Server.max_accounts', $lang->get('maximum_accounts')); ?>

                    <?php echo $forms->input('Server.priority', $lang->get('deployment_priority')); ?>
                    <span class="help-block"><?php echo $lang->get('deployment_priority_help_text'); ?></span>

                    <?php echo $forms->checkbox('Server.is_active', $lang->get('active')); ?>

                    <?php echo $forms->textarea('Server.notes', $lang->get('notes'), array('value' => App::get('security')->decrypt($server->notes))); ?>

                    <?php echo $forms->input('Server.status_url', $lang->get('status_url')); ?>
                    <span class="help-block"><?php echo $lang->get('status_url_help_text'); ?></span>

                    <div class="form-actions">
                        <?php echo $forms->submit('submit', $lang->get('save')); ?>
                    </div>
                <?php echo $forms->close(); ?>
            </div>
        </div>
    </div>
    <?php if($accounts->count() < 1): ?>
    <div class="col-lg-12">
        <div class="panel panel-secondary">
            <div class="panel-content text-center">
                <a href="<?php echo $router->generate('admin-server-delete', array('id' => $group->id, 'server_id' => $server->id)); ?>" class="btn btn-danger btn-large" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');">
                    <?php echo $lang->get('server_delete'); ?>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
