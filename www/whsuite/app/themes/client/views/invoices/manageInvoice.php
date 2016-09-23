<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="well">
                            <p><b><?php echo $lang->get('invoice_to'); ?>:</b></p>
                            <p><?php echo $client->first_name; ?> <?php echo $client->last_name; ?></p>
                            <p></p>
                            <p>
                                <?php echo $client->address1; ?><br>
                                <?php if ($client->address2 !=''): ?>
                                    <?php echo $client->address2; ?><br>
                                <?php endif; ?>
                                <?php echo $client->city; ?><br>
                                <?php echo $client->state; ?><br>
                                <?php echo $client->postcode; ?><br>
                                <?php echo $client->country; ?>
                            </p>
                            <p>
                                <b><?php echo $lang->get('email'); ?>:</b> <?php echo $client->email; ?><br>
                                <b><?php echo $lang->get('telephone'); ?>:</b> <?php echo $client->phone; ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <h2><?php echo $lang->get(Invoice::$status_types[$invoice->status]); ?></h2>
                        <p>
                            <b><?php echo $lang->get('date_created'); ?>: </b>
                            <?php
                                $Carbon = \Carbon\Carbon::parse(
                                    $invoice->created_at,
                                    $date['timezone']
                                );
                                echo $Carbon->format($date['short_date']);
                            ?>
                            <br>
                            <b><?php echo $lang->get('date_due'); ?>: </b>
                            <?php
                                $Carbon = \Carbon\Carbon::parse(
                                    $invoice->date_due,
                                    $date['timezone']
                                );
                                echo $Carbon->format($date['short_date']);
                            ?>
                            <br>
                            <b><?php echo $lang->get('date_paid'); ?>: </b>
                            <?php if ($invoice->date_paid > '1970-01-01 00:00:00'): ?>
                                <?php
                                    $Carbon = \Carbon\Carbon::parse(
                                        $invoice->date_paid,
                                        $date['timezone']
                                    );
                                    echo $Carbon->format($date['short_date']);
                                ?>
                            <?php else: ?>
                                <?php echo $lang->get('not_available'); ?>
                            <?php endif; ?>
                        </p>
                        <?php if($client->is_taxexempt == '1'): ?>

                        <p class="alert alert-info"><?php echo $lang->get('client_is_tax_exempt'); ?></p>
                        <?php endif; ?>
                        <hr>
                        <p>
                            <a href="<?php echo $router->generate('client-invoice-download', array('id' => $invoice->id)); ?>" class="btn btn-default">
                                <?php echo $lang->get('download_pdf'); ?>
                            </a>
                            <?php if($invoice->status < 1): ?>
                                <a href="<?php echo $router->generate('client-invoice-pay', array('id' => $invoice->id)); ?>" class="btn btn-primary">
                                    <?php echo $lang->get('pay_invoice'); ?>
                                </a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th width="66%"><?php echo $lang->get('item'); ?></th>
                            <th width="10%" class="text-right"><?php echo $lang->get('unit_price'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($invoice_items as $item): ?>
                        <tr>
                            <td><?php echo $item->description; ?></td>
                            <td class="text-right"><?php echo App::get('money')->format($item->total, $invoice->currency_id, false, true); ?></td>
                        </tr>

                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right">
                                <b><?php echo $lang->get('subtotal'); ?>:</b> <?php echo App::get('money')->format($invoice->subtotal, $invoice->currency_id, false, true); ?>
                            </td>
                        </tr>
                        <?php if ($invoice->pre_tax_discount > 0): ?>
                        <tr>
                            <td colspan="5" class="text-right">
                                <b><?php echo $lang->get('pre_tax_discount'); ?>:</b> -<?php echo App::get('money')->format($invoice->pre_tax_discount, $invoice->currency_id, false, true); ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if($invoice->level1_total > 0): ?>
                        <tr>
                            <td colspan="5" class="text-right">
                                <b><?php echo $lang->get('level_1_tax'); ?>:</b> <?php echo App::get('money')->format($invoice->level1_total, $invoice->currency_id, false, true); ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if($invoice->level2_total > 0): ?>
                        <tr>
                            <td colspan="5" class="text-right">
                                <b><?php echo $lang->get('level_2_tax'); ?>:</b> <?php echo App::get('money')->format($invoice->level2_total, $invoice->currency_id, false, true); ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if($invoice->post_tax_discount > 0): ?>
                        <tr>
                            <td colspan="5" class="text-right">
                                <b><?php echo $lang->get('post_tax_discount'); ?>:</b> -<?php echo App::get('money')->format($invoice->post_tax_discount, $invoice->currency_id, false, true); ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="5" class="text-right">
                                <b><?php echo $lang->get('total'); ?>:</b> <?php echo App::get('money')->format($invoice->total, $invoice->currency_id, false, true); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right">
                                <b><?php echo $lang->get('total_paid'); ?>:</b> <?php echo App::get('money')->format($invoice->total_paid, $invoice->currency_id, false, true); ?>
                            </td>
                        </tr>
                        <?php if (($invoice->total-$invoice->total_paid) > 0): ?>
                            <tr>
                                <td colspan="5" class="text-right">
                                    <b><?php echo $lang->get('total_remainin_due'); ?>:</b> <?php echo App::get('money')->format(($invoice->total-$invoice->total_paid), $invoice->currency_id, false, true); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
                <hr>

                <h3><?php echo $lang->get('applied_transactions'); ?></h3>
                <?php if(count($transactions) > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th data-hide="phone,tablet"><?php echo $lang->get('transaction_id'); ?></th>
                                <th><?php echo $lang->get('date'); ?></th>
                                <th><?php echo $lang->get('amount'); ?></th>
                                <th><?php echo $lang->get('status'); ?></th>
                                <th data-hide="phone,tablet"><?php echo $lang->get('description'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td>#<?php echo $transaction->id; ?></td>
                                <td>
                                    <?php
                                        $Carbon = \Carbon\Carbon::parse(
                                            $transaction->created_at,
                                            $date['timezone']
                                        );
                                        echo $Carbon->format($date['short_date']);
                                    ?>
                                </td>
                                <td><?php echo App::get('money')->format($transaction->amount, $transaction->currency_id, false, true);?></td>
                                <td>
                                    <?php
                                        echo \App\Libraries\Transactions::formatTransactionType($transaction->type);
                                    ?>
                                </td>
                                <td><?php echo $transaction->description; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info"><?php echo $lang->get('no_transactions_applied'); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
