<div class="col-md-4">
    <div class="panel panel-secondary">
        <div class="panel-heading">Recent Orders</div>
        <div class="panel-content panel-table">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th><?php echo $lang->get('order_no'); ?></th>
                        <th><?php echo $lang->get('client'); ?></th>
                        <th><?php echo $lang->get('total'); ?></th>
                        <th class="text-center"><?php echo $lang->get('status'); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) < 1): ?>
                        <tr>
                            <td colspan="5" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order->order_no; ?></td>
                            <td>
                                <?php
                                    $client = $order->Client()->first();
                                    echo $client->first_name.' '.$client->last_name;
                                ?>
                            </td>
                            <td>
                                <?php
                                    $invoice = $order->Invoice()->first();
                                    echo \App::get('money')->format($invoice->total, $invoice->currecy_id);
                                ?>
                            </td>
                            <td class="text-center">
                                <?php echo \App::get('orderhelper')->getOrderStatus($order); ?>
                            </td>
                            <td class="text-right">
                                <a href="<?php echo $router->generate('admin-order-view', array('id' => $order->id)); ?>" class="btn btn-primary btn-mini">
                                    <span class="fa fa-search"></span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">
                            <a href="<?php echo $router->generate('admin-order'); ?>">
                                <?php echo $lang->get('view_all'); ?>
                            </a>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
