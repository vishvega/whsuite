<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <?php if(isset($client)): ?>
                <div class="col-lg-4">
                    <?php echo $view->fetch('clients/profileSidebar/info.php'); ?>
                </div>
                <div class="col-lg-8">
                <?php else: ?>
                <div class="col-lg-12">
                <?php endif; ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('transactions'); ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('date'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('amount'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('gateway'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('client'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('status'); ?></th>
                                        <th data-hide="phone,tablet"><?php echo $lang->get('description'); ?></th>
                                        <th data-hide="phone,tablet"><?php echo $lang->get('transaction_id'); ?></th>
                                        <th data-hide="phone"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($transactions as $transaction):
                                        $currency = $transaction->Currency()->first();
                                        $gateway = $transaction->Gateway()->first();
                                        $invoice = $transaction->Invoice()->first();
                                        $client = $transaction->Client()->first();
                                    ?>
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
                                        <td>
                                            <?php
                                            if($transaction->type == 'debit'):
                                                echo '-';
                                            endif;
                                            ?>
                                            <?php echo App::get('money')->format($transaction->amount, $currency->code);?>
                                        </td>

                                        <td>
                                            <?php
                                            if ($gateway):
                                                echo $gateway->name;
                                            else:
                                                echo $lang->get('not_available');
                                            endif;
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client-profile', array('id' => $transaction->client_id)); ?>">
                                                <?php echo $client->first_name; ?> <?php echo $client->last_name; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php
                                                echo \App\Libraries\Transactions::formatTransactionType($transaction->type);
                                            ?>
                                        </td>
                                        <td><?php echo $transaction->description; ?></td>
                                        <td>#<?php echo $transaction->id; ?></td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client-manage-transaction', array('id' => $transaction->client_id, 'transaction_id' => $transaction->id)); ?>" class="btn btn-small btn-primary">
                                                <?php echo $lang->get('manage'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8" class="text-right"><?php echo $pagination; ?></td>
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
