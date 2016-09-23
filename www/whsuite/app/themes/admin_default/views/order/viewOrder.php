<?php echo $view->fetch('elements/header.php'); ?>
    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <?php echo $view->fetch('clients/profileSidebar/info.php'); ?>
                </div>
                <div class="col-lg-8">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <?php echo $lang->get('order_details'); ?>
                        </div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td width="25%"><b><?php echo $lang->get('order_no'); ?>:</b></td>
                                        <td width="25%">#<?php echo $order->order_no; ?></td>
                                        <td width="25%"><b><?php echo $lang->get('client'); ?>:</b></td>
                                        <td width="25%">
                                            <a href="<?php echo $router->generate('admin-client-profile', array('id' => $client->id)); ?>">
                                                <?php echo $client->first_name.' '.$client->last_name; ?> (#<?php echo $client->id; ?>)
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b><?php echo $lang->get('created_at'); ?>:</b></td>
                                        <td width="25%">
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $order->created_at,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_datetime']);
                                            ?>
                                        </td>
                                        <td width="25%"><b><?php echo $lang->get('origin_ip'); ?>:</b></td>
                                        <td width="25%"><?php echo $order->user_ip; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b><?php echo $lang->get('invoice_no'); ?>:</b></td>
                                        <td width="25%">
                                            <a href="<?php echo $router->generate('admin-client-invoice', array('id' => $client->id, 'invoice_id' => $invoice->id)); ?>">
                                                #<?php echo $invoice->invoice_no; ?>
                                            </a>
                                        </td>
                                        <td width="25%"><b><?php echo $lang->get('origin_hostname'); ?>:</b></td>
                                        <td width="25%"><?php echo $order->user_hostname; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b><?php echo $lang->get('payment_status'); ?>:</b></td>
                                        <td width="25%">
                                            <?php if($invoice->status == '1'): ?>
                                                <span class="label label-success"><?php echo $lang->get('paid'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-danger"><?php echo $lang->get('unpaid'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td width="25%"><b><?php echo $lang->get('order_status'); ?>:</b></td>
                                        <td width="25%">
                                            <?php echo \App::get('orderhelper')->getOrderStatus($order); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <?php echo $lang->get('order_items'); ?>
                        </div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('description'); ?></th>
                                        <th><?php echo $lang->get('price'); ?></th>
                                        <th><?php echo $lang->get('status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($purchases->count() > 0): ?>
                                        <?php foreach($purchases as $purchase): ?>
                                            <?php
                                            $product = $purchase->Product()->first();
                                            $product_type = $product->ProductType()->first();
                                            $period = $purchase->BillingPeriod()->first();
                                            $purchased_addons = $purchase->ProductAddonPurchase()->get();
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php if($purchase->status == '1'): ?>
                                                        <a href="<?php echo $router->generate('admin-client-service', array('id' => $purchase->client_id, 'service_id' => $purchase->id)); ?>">
                                                    <?php endif; ?>

                                                    <?php if($product_type->is_hosting == '1'): ?>
                                                        <?php $hosting = $purchase->Hosting()->first(); ?>
                                                        <?php echo $product->name; ?> (<?php echo $hosting->domain; ?>)
                                                    <?php elseif($product_type->is_domain == '1'): ?>
                                                        <?php $domain = $purchase->Domain()->first(); ?>
                                                        <?php echo $product->name; ?> (<?php echo $domain->domain; ?>)
                                                    <?php else: ?>
                                                        <?php echo $product->name; ?>
                                                    <?php endif; ?>

                                                    <?php if($purchase->status == '1'): ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo App::get('money')->format($purchase->first_payment, $purchase->currency_id); ?>
                                                </td>
                                                <td>
                                                    <?php if($purchase->status == '1'): ?>
                                                        <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                                                    <?php elseif($purchase->status == '2'): ?>
                                                        <span class="label label-info"><?php echo $lang->get('suspended'); ?></span>
                                                    <?php elseif($purchase->status == '3'): ?>
                                                        <span class="label label-danger"><?php echo $lang->get('terminated'); ?></span>
                                                    <?php else: ?>
                                                        <span class="label label-warning"><?php echo $lang->get('pending'); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>

                                            <?php if($purchased_addons->count() > 0): ?>
                                                <?php foreach($purchased_addons as $addon): ?>
                                                <?php $product_addon = $addon->ProductAddon()->first(); ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $lang->get('addon'); ?>: <?php echo $product_addon->name; ?>
                                                        </td>
                                                        <td><?php echo $lang->get($period->name); ?></td>
                                                        <td><?php echo App::get('money')->format($addon->first_payment, $addon->currency_id); ?></td>
                                                        <td>
                                                            <?php if($addon->is_active == '1'): ?>
                                                                <span class="label label-success"><?php echo $lang->get('actve'); ?></span>
                                                            <?php else: ?>
                                                                <span class="label label-warning"><?php echo $lang->get('inactive'); ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4"><?php echo $lang->get('no_results_found'); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel panel-primary">
                        <div class="panel-content text-center">
                            <a href="<?php echo $router->generate('admin-order-activate', array('id' => $order->id)); ?>" class="btn btn-success">
                                <?php echo $lang->get('activate_order'); ?>
                            </a>
                            <a href="<?php echo $router->generate('admin-order-pending', array('id' => $order->id)); ?>" class="btn btn-info">
                                <?php echo $lang->get('set_to_pending'); ?>
                            </a>
                            <a href="#" data-toggle="modal" data-target="#terminateModal" class="btn btn-danger">
                                <?php echo $lang->get('terminate_order'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="terminateModal" tabindex="-1" role="dialog" aria-labelledby="terminateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo $lang->get('warning'); ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php echo $lang->get('confirm_order_termination'); ?></p>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-default" data-dismiss="modal"><?php echo $lang->get('cancel'); ?></a>
                    <a href="<?php echo $router->generate('admin-order-terminate', array('id' => $order->id)); ?>" class="btn btn-danger"><?php echo $lang->get('terminate_order'); ?></a>
                </div>
            </div>
        </div>
    </div>

    <?php echo $view->fetch('elements/footer.php'); ?>
