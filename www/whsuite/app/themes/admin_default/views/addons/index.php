<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <?php echo $lang->get('addon_management'); ?>
                        </div>
                        <div class="panel-content panel-table">
                            <table class="table striped">
                                <tbody>
                                <?php foreach($addons as $addon_slug => $addon): ?>
                                    <tr>
                                        <td class="col-md-2">
                                            <img src="<?php echo $assets->image($addon->logo); ?>" width="100%">
                                        </td>
                                        <td class="col-md-7">
                                            <p>
                                                <b><?php echo $addon->details['name']; ?></b>
                                                (<?php echo $lang->get('version'); ?>: <?php echo $addon->details['version']; ?>
                                                <?php if(isset($addon->data->version)): ?>
                                                    , <?php echo $lang->get('installed_version'); ?>: <?php echo $addon->data->version; ?>
                                                <?php endif; ?>
                                                )
                                            </p>
                                            <p><?php echo $addon->details['description']; ?></p>
                                        </td>
                                        <td class="com-md-3 text-right">
                                            <?php if(isset($addon->data->version) && $addon->data->version != $addon->details['version']): ?>
                                                <a href="<?php echo $router->generate('admin-addon-update', array('id' => $addon->data->id)); ?>" class="btn btn-success btn-small"><?php echo $lang->get('update'); ?></a>
                                            <?php endif; ?>

                                            <?php if(isset($addon->data->is_active)): ?>
                                                <?php if($addon->data->is_active == '1'): ?>
                                                    <a href="<?php echo $router->generate('admin-addon-disable', array('id' => $addon->data->id)); ?>" class="btn btn-default btn-small"><?php echo $lang->get('disable'); ?></a>
                                                <?php else: ?>
                                                    <a href="<?php echo $router->generate('admin-addon-enable', array('id' => $addon->data->id)); ?>" class="btn btn-success btn-small"><?php echo $lang->get('enable'); ?></a>
                                                <?php endif; ?>
                                                <a href="<?php echo $router->generate('admin-addon-uninstall', array('id' => $addon->data->id)); ?>" class="btn btn-danger btn-small"><?php echo $lang->get('uninstall'); ?></a>
                                            <?php else: ?>
                                                <a href="<?php echo $router->generate('admin-addon-install', array('slug' => $addon_slug)); ?>" class="btn btn-success btn-small"><?php echo $lang->get('install'); ?></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $view->fetch('elements/footer.php'); ?>
