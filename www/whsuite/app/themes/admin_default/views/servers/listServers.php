<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">

                                <?php if($server_groups->count() < 1): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                                    </tr>
                                <?php else: ?>

                                    <?php
                                    foreach($server_groups as $group):
                                        $servers = $group->Server()->get();
                                    ?>
                                    <thead>
                                        <tr>
                                            <th colspan="3"><?php echo $group->name; ?></th>
                                            <th colspan="2" class="text-right">
                                                <a href="<?php echo $router->generate('admin-server-add', array('id' => $group->id)); ?>" class="btn btn-primary btn-small"><?php echo $lang->get('server_add', array('id' => $group->id)); ?></a>
                                                <a href="<?php echo $router->generate('admin-servergroup-manage', array('id' => $group->id)); ?>" class="btn btn-primary btn-small"><?php echo $lang->get('manage_group'); ?></a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($servers->count() < 1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach ($servers as $server): ?>
                                            <tr>
                                                <td><?php echo $server->name; ?> (<?php echo $server->hostname; ?>)</td>
                                                <td><?php echo $server->main_ip; ?></td>
                                                <td><?php echo $server->location; ?></td>
                                                <td>
                                                <?php if($server->is_active == '1'): ?>
                                                    <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                                                <?php else: ?>
                                                    <span class="label label-danger"><?php echo $lang->get('inactive'); ?></span>
                                                <?php endif; ?>

                                                <?php if($server->id === $group->default_server_id): ?>
                                                    <span class="label label-info"><?php echo $lang->get('default_server'); ?></span>
                                                <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?php echo $router->generate('admin-server-manage', array('id' => $group->id, 'server_id' => $server->id)); ?>" class="btn btn-secondary btn-small"><?php echo $lang->get('manage_server'); ?></a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php');
