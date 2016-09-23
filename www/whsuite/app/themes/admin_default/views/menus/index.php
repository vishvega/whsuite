<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('menu'); ?></th>
                                        <th><?php echo $lang->get('links'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($menus->count() > 0): ?>
                                        <?php foreach($menus as $menu): ?>
                                            <tr>
                                                <td><?php echo $menu->name; ?></td>
                                                <td><?php echo $menu->links()->count(); ?></td>
                                                <td class="text-right">
                                                    <a href="<?php echo $router->generate('admin-menu-manage', array('id' => $menu->id)); ?>" class="btn btn-primary btn-small"><?php echo $lang->get('manage'); ?></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <?php echo $lang->get('no_results_found'); ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>
