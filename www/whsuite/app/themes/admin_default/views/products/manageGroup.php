<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('update_product_group'); ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-productgroup-manage', array('id' => $group->id)))); ?>

                                <?php echo $forms->input('Group.name', $lang->get('name')); ?>

                                <?php echo $forms->textarea('Group.description', $lang->get('description')); ?>

                                <?php echo $forms->checkbox('Group.is_visible', $lang->get('is_visible')); ?>

                                <?php echo $forms->input('Group.sort', $lang->get('sort')); ?>

                                <?php echo $group->customFields(false); ?>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>

                <?php if($products->count() < 1): ?>
                    <div class="col-lg-12">
                        <div class="panel panel-secondary">
                            <div class="panel-content text-center">
                                <a href="<?php echo $router->generate('admin-productgroup-delete', array('id' => $group->id)); ?>" class="btn btn-danger btn-large" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');">
                                    <?php echo $lang->get('delete_product_group'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>

                    <div class="col-lg-12">
                        <div class="panel panel-secondary">
                            <div class="panel-heading"><?php echo $lang->get('product_management'); ?></div>
                            <div class="panel-content panel-table">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo $lang->get('name'); ?></th>
                                            <th><?php echo $lang->get('type'); ?></th>
                                            <th><?php echo $lang->get('status'); ?></th>
                                            <th><?php echo $lang->get('accounts'); ?></th>
                                            <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach($products as $product):
                                        $type = $product->ProductType()->first();

                                        if($product->server_group_id > 0):
                                            $server_group = $product->ServerGroup()->first();
                                            $server_module = null;

                                            if (is_object($server_group)):

                                                $server_module = $server_group->ServerModule()->first();
                                            endif;
                                        else:
                                            $server_group = null;
                                            $server_module = null;
                                        endif;
                                    ?>
                                        <tr>
                                            <td><?php echo $product->name; ?></td>
                                            <td>
                                                <?php echo $type->name; ?>
                                                <?php if(isset($server_module)): ?>
                                                    (<?php echo $server_module->name; ?>)
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                            <?php if($product->is_active == '1'): ?>
                                                <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-danger"><?php echo $lang->get('inactive'); ?></span>
                                            <?php endif; ?>

                                            <?php if($product->is_visible == '0'): ?>
                                                <span class="label label-info"><?php echo $lang->get('hidden'); ?></span>
                                            <?php endif; ?>
                                            </td>

                                            <td><?php echo $product->ProductPurchase()->count(); ?></td>
                                            <td class="text-right">
                                                <a href="<?php echo $router->generate('admin-product-manage', array('id' => $group->id, 'product_id' => $product->id)); ?>" class="btn btn-primary btn-small"><?php echo $lang->get('manage_product'); ?></a>
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
