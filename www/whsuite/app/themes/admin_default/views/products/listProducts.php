<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">

                                <?php if($product_groups->count() < 1): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                                    </tr>
                                <?php else: ?>

                                    <?php
                                    foreach($product_groups as $group):
                                        $products = $group->Product()->get();
                                    ?>
                                    <thead>
                                        <tr>
                                            <th colspan="3"><?php echo $group->name; ?></th>
                                            <th class="text-right">
                                                <a href="<?php echo $router->generate('admin-product-add', array('id' => $group->id)); ?>" class="btn btn-primary btn-small"><?php echo $lang->get('new_product', array('id' => $group->id)); ?></a>
                                                <a href="<?php echo $router->generate('admin-productgroup-manage', array('id' => $group->id)); ?>" class="btn btn-primary btn-small"><?php echo $lang->get('manage_group'); ?></a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($products->count() < 1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                                        </tr>
                                        <?php else: ?>
                                            <?php
                                            foreach ($products as $product):
                                                $type = $product->ProductType()->first();

                                                if($product->server_group_id > 0):
                                                    $server_group = $product->serverGroup()->first();
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
                                                <td class="text-right">
                                                    <a href="<?php echo $router->generate('admin-product-manage', array('id' => $group->id, 'product_id' => $product->id)); ?>" class="btn btn-secondary btn-small"><?php echo $lang->get('manage_product'); ?></a>
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
