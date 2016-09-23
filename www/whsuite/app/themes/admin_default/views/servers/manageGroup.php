<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('update_server_group'); ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-servergroup-manage', array('id' => $group->id)))); ?>

                                <?php echo $forms->input('Group.name', $lang->get('name')); ?>

                                <?php echo $forms->textarea('Group.description', $lang->get('description')); ?>

                                <?php echo $forms->checkbox('Group.autofill', $lang->get('auto_fill_servers')); ?>
                                <span class="help-block"><?php echo $lang->get('auto_fill_servers_help_text'); ?></span>

                                <?php echo $forms->select('Group.server_module_id', $lang->get('server_module'), array('options' => $server_modules)); ?>
                                <span class="help-block"><?php echo $lang->get('server_module_help_text'); ?></span>

                                <?php echo $forms->select('Group.default_server_id', $lang->get('default_server'), array('options' => $server_list)); ?>
                                <span class="help-block"><?php echo $lang->get('default_server_help_text'); ?></span>

                                <?php echo $group->customFields(false); ?>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>

                <?php if($servers->count() < 1): ?>
                    <div class="col-lg-12">
                        <div class="panel panel-secondary">
                            <div class="panel-content text-center">
                                <a href="<?php echo $router->generate('admin-servergroup-delete', array('id' => $group->id)); ?>" class="btn btn-danger btn-large" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');">
                                    <?php echo $lang->get('servergroup_delete'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>

                    <div class="col-lg-12">
                        <div class="panel panel-secondary">
                            <div class="panel-heading"><?php echo $lang->get('server_management'); ?></div>
                            <div class="panel-content panel-table">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo $lang->get('name'); ?></th>
                                            <th><?php echo $lang->get('location'); ?></th>
                                            <th><?php echo $lang->get('main_ip'); ?></th>
                                            <th><?php echo $lang->get('accounts'); ?></th>
                                            <th><?php echo $lang->get('deployment_priority'); ?></th>
                                            <th><?php echo $lang->get('manage'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($servers as $server): ?>
                                        <tr>
                                            <td><?php echo $server->name; ?></td>
                                            <td><?php echo $server->location; ?></td>
                                            <td><?php echo $server->main_ip; ?></td>
                                            <td>
                                                <?php echo $server->totalAccounts(); ?> /
                                                <?php
                                                if($server->max_accounts > 0):
                                                    echo $server->max_accounts;
                                                else:
                                                    echo $lang->get('unlimited');
                                                endif;
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $server->priority; ?>
                                                <?php if($group->default_server_id === $server->id): ?>
                                                    <span class="label label-info"><?php echo $lang->get('default_server'); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo $router->generate('admin-server-manage', array('id' => $group->id, 'server_id' => $server->id)); ?>" class="btn btn-small btn-primary">
                                                    <?php echo $lang->get('manage_server'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


<?php echo $view->fetch('elements/footer.php'); ?>
