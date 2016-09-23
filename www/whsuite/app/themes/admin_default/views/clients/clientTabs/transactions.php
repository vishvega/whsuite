<div class="panel panel-secondary">
    <div class="panel-heading"><?php echo $lang->get('transactions'); ?></div>
    <div class="panel-content">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo $lang->get('date'); ?></th>
                    <th><?php echo $lang->get('amount'); ?></th>
                    <th><?php echo $lang->get('status'); ?></th>
                    <th data-hide="phone"><?php echo $lang->get('gateway'); ?></th>
                    <th data-hide="phone,tablet"><?php echo $lang->get('description'); ?></th>
                    <th data-hide="phone,tablet"><?php echo $lang->get('invoice_no'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if($transactions->count() > 0): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td>
                            <?php
                                $Carbon = \Carbon\Carbon::parse(
                                    $transaction->created_at,
                                    $date['timezone']
                                );
                                echo $Carbon->format($date['short_date']);
                            ?>
                        </td>
                        <td><?php echo App::get('money')->format($transaction->amount, $transaction->Currency->code);?></td>
                        <td>
                            <?php
                                echo \App\Libraries\Transactions::formatTransactionType($transaction->type);
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($transaction->Gateway):
                                echo $transaction->Gateway->slug;
                            else:
                                echo $lang->get('not_available');
                            endif;
                            ?>
                        </td>
                        <td><?php echo $transaction->description; ?></td>
                        <td>
                            <?php
                            if($transaction->Invoice):
                                echo $transaction->Invoice->invoice_no;
                            else:
                                echo $lang->get('not_available');
                            endif;
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo $router->generate('admin-client-manage-transaction', array('id' => $transaction->client_id, 'transaction_id' => $transaction->id)); ?>" class="btn btn-small btn-primary">
                                <?php echo $lang->get('manage'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="row">
                            <div class="col-lg-6 text-center">
                                <a href="<?php echo $router->generate('admin-client-new-transaction', array('id' => $client->id)); ?>" class="btn btn-small btn-primary">
                                    <i class="fa fa-plus"></i> <?php echo $lang->get('new_transaction'); ?>
                                </a>
                            </div>
                            <div class="col-lg-6 text-center">
                                <a href="<?php echo $router->generate('admin-client-transactions', array('id' => $client->id)); ?>" class="btn btn-small btn-primary">
                                    <i class="fa fa-list"></i> <?php echo $lang->get('all_transactions'); ?>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
