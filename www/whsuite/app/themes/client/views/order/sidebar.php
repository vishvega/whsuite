<div class="panel panel-primary">
    <div class="panel-heading"><?php echo $lang->get('shopping_cart'); ?></div>
    <div class="panel-body">
        <?php if(!empty($cart_items)): ?>

            <?php foreach($cart_data['items'] as $cart_item_id => $cart_item): ?>
                <div class="cart-row">
                    <b><?php echo $cart_item['name']; ?></b><br>
                    <?php if (! empty($cart_items[$cart_item_id]['domain'])): ?>
                        <i><?php echo $cart_items[$cart_item_id]['domain']; ?></i><br>
                    <?php endif; ?>
                    <small>
                        <?php echo $cart_item['period']; ?> - <?php echo App::get('money')->format($cart_item['price'], $cart_data['currency_id'], false, false); ?>
                        <?php if($cart_item['setup_fee'] > 0): ?>
                            (<?php echo App::get('money')->format($item['setup_fee'], $cart_data['currency_id'], false, false); ?> <?php echo $lang->get('setup_fee'); ?>)
                        <?php endif; ?>
                    </small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="cart-row text-center">
            <?php echo $lang->get('cart_is_empty'); ?>
        </div>
        <?php endif; ?>

        <div class="cart-details">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="text-right"><?php echo $lang->get('subtotal'); ?>:</td>
                        <td class="text-right"><?php echo App::get('money')->format($cart_data['sub_total'], $cart_data['currency_id'], false, true); ?></td>
                    </tr>
                    <tr>
                        <td class="text-right"><?php echo $lang->get('tax'); ?>:</td>
                        <td class="text-right"><?php echo App::get('money')->format($cart_data['total_taxed'], $cart_data['currency_id'], false, true); ?></td>
                    </tr>
                    <tr>
                        <td class="text-right"><?php echo $lang->get('total'); ?>:</td>
                        <td class="text-right"><?php echo App::get('money')->format($cart_data['total'], $cart_data['currency_id'], false, true); ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right">
                            <a href="<?php echo $router->generate('client-view-cart'); ?>" class="btn btn-default btn-sm"><?php echo $lang->get('checkout'); ?></a>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
