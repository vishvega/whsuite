<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('name'); ?></th>
                                        <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($groups) < 1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php foreach ($groups as $group): ?>
                                    <tr>
                                        <td><?php echo $lang->get($group->name); ?></td>
                                        <td class="text-right">
                                            <a href="<?php echo $router->generate('admin-custom-fields-view-group', array('id' => $group->id)); ?>" class="btn btn-primary">
                                                <?php echo $lang->get('manage'); ?>
                                            </a>
                                            <?php if(DEV_MODE): ?>
                                                <a href="<?php echo $router->generate('admin-custom-fields-delete-group', array('id' => $group->id)); ?>" class="btn btn-danger" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');">
                                                    <?php echo $lang->get('delete'); ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right"><?php echo $pagination; ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php');
