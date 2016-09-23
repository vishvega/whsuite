<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('accounts'); ?> (<?php echo $accounts->count(); ?>)</div>
            <div class="panel-content panel-table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang->get('service'); ?></th>
                            <th><?php echo $lang->get('client'); ?></th>
                            <th><?php echo $lang->get('date_created'); ?></th>
                            <th><?php echo $lang->get('date_renewal'); ?></th>
                            <th><?php echo $lang->get('status'); ?></th>
                            <th><?php echo $lang->get('manage'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($accounts->count() < 1): ?>
                        <tr>
                            <td colspan="6" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                        </tr>
                    <?php else: ?>
                        <?php
                        foreach($accounts as $account):
                            $purchase = $account->ProductPurchase()->first();
                            $product = $purchase->Product()->first();
                            $client = $purchase->Client()->first();
                        ?>
                        <tr>
                            <td><?php echo $product->name.' ('.$account->domain.')'; ?></td>
                            <td>
                                <a href="<?php echo $router->generate('admin-client-profile', array('id' => $client->id)); ?>">
                                    <?php echo $client->first_name.' '.$client->last_name; ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                    $Carbon = \Carbon\Carbon::parse(
                                        $purchase->created_at,
                                        $date['timezone']
                                    );
                                    echo $Carbon->format($date['short_date']);
                                ?>
                            </td>
                            <td>
                                <?php
                                    $Carbon = \Carbon\Carbon::parse(
                                        $purchase->next_renewal,
                                        $date['timezone']
                                    );
                                    echo $Carbon->format($date['short_date']);
                                ?>
                            </td>
                            <td>
                                <?php if ($purchase->status == '1'): ?>
                                    <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                                <?php elseif ($purchase->status == '2'): ?>
                                    <span class="label label-warning"><?php echo $lang->get('suspended'); ?></span>
                                <?php elseif ($purchase->status == '3'): ?>
                                    <span class="label label-important"><?php echo $lang->get('terminated'); ?></span>
                                <?php else: ?>
                                    <span class="label label-warning"><?php echo $lang->get('pending'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <a href="<?php echo $router->generate('admin-client-service', array('id' => $client->id, 'service_id' => $purchase->id)); ?>" class="btn btn-primary btn-small">
                                    <?php echo $lang->get('manage'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
