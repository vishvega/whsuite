<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $lang->get('shopping_cart'); ?></div>
            <div class="panel-body">

                <?php foreach($cart['items'] as $item_id => $item): ?>
                    <div class="cart-row">
                        <div class="row">
                            <div class="col-md-9">
                                <b><?php echo $item['name']; ?></b>
                                <small>
                                    (<a href="<?php echo $router->generate('client-order-delete-item', array('product_id' => $item['product_id'], 'item_id' => $item_id)); ?>" class="text-danger">
                                        <?php echo $lang->get('delete'); ?>
                                    </a>)
                                </small>
                                <?php if($item['period']): ?>
                                <br>
                                <small><?php echo $lang->get('billing_period'); ?>: <?php echo $item['period']; ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3 text-right">
                                <?php echo App::get('money')->format($item['price'], $cart['currency_id'], false, true); ?>
                                <?php if($item['setup_fee'] > 0): ?>
                                    <br><small>(<?php echo $lang->get('setup'); ?>: <?php echo App::get('money')->format($item['setup_fee'], $cart['currency_id'], false, true); ?>)</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(!empty($item['addons'])): ?>
                        <b><?php echo $lang->get('addon_items'); ?></b>
                        <div class="well well-sm">

                            <?php foreach($item['addons'] as $addon): ?>
                                <div class="row">
                                    <div class="col-md-9">
                                        <small><b><?php echo $addon['name']; ?></b></small>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <small><?php echo App::get('money')->format($addon['price'], $cart['currency_id'], false, true); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="cart-details">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td class="text-right">
                                    <b><?php echo $lang->get('subtotal'); ?>:</b>
                                    <?php echo App::get('money')->format($cart['sub_total'], $cart['currency_id'], false, true); ?>
                                </td>
                            </tr>
                            <?php if($cart['total_taxed'] > 0): ?>
                            <tr>
                                <td class="text-right">
                                    <b><?php echo $lang->get('tax'); ?>:</b>
                                    <?php echo App::get('money')->format($cart['total_taxed'], $cart['currency_id'], false, true); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-right">
                                    <b><?php echo $lang->get('total_due'); ?>:</b>
                                    <?php echo App::get('money')->format($cart['total'], $cart['currency_id'], false, true); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php if($logged_in): ?>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php echo $forms->open(array('action', $router->generate('client-view-cart'), 'method' => 'post', 'class' => '')); ?>
                            <?php echo $forms->submit('checkout', $lang->get('checkout'), array('class' => 'btn btn-primary btn-lg')); ?>
                        <?php echo $forms->close(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if(!$logged_in): ?>
    <?php echo $view->fetch('order/cart/login_register.php'); ?>
<?php endif; ?>


<?php echo $view->fetch('elements/footer.php'); ?>
