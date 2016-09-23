<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default" id="account-details">
            <div class="panel-heading"><?php echo $lang->get('new_order'); ?>: <?php echo $product->name; ?></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-9">
                        <?php echo $forms->open(array('action' => $router->generate('client-order-new-item', array('product_id' => $product->id)), 'method' => 'post')); ?>
                            <?php echo $forms->hidden('product_id'); ?>

                            <?php echo $form; ?>

                            <?php if(!empty($product_addon_list)): ?>
                            <h3><?php echo $lang->get('addon_items'); ?></h3>
                                <?php foreach($product_addon_list as $product_addon): ?>

                                    <div class="well well-sm">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h4 class="nomargin"><?php echo $product_addon['product_addon']->name; ?></h4>
                                            </div>
                                            <div class="col-md-4">
                                                <?php echo $forms->checkbox('product_addon.'.$product_addon['product_addon']->id, $lang->get('order').' ('.App::get('money')->format($product_addon['pricing']->price, $product_addon['pricing']->currency_id, false, true).')'); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php echo $product_addon['product_addon']->description; ?>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php endif; ?>

                            <div class="form-actions">
                                <?php echo $forms->submit('add_to_cart', $lang->get('add_to_cart')); ?>
                            </div>


                        <?php echo $forms->close(); ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo $view->fetch('order/sidebar.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
