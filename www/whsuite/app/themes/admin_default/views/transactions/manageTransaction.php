<?php echo $view->fetch('elements/header.php'); ?>
    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <?php echo $view->fetch('clients/profileSidebar/info.php'); ?>
                </div>
                <div class="col-lg-8">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('manage_transaction'); ?></div>
                        <div class="panel-content">

                            <?php if($transaction->type == 'void'): ?>
                                <p class="alert alert-danger text-center"><?php echo $lang->get('void'); ?></p>
                            <?php elseif($transaction->type == 'refunded'): ?>
                                <p class="alert alert-danger text-center"><?php echo $lang->get('refunded'); ?></p>
                            <?php endif; ?>

                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td><strong><?php echo $lang->get('type'); ?></strong></td>
                                        <td>
                                            <?php if($transaction->type == 'receipt' || $transaction->type == 'credit'): ?>
                                                <?php echo $lang->get('credit_note'); ?>
                                            <?php elseif($transaction->type == 'invoice'): ?>
                                                <?php echo $lang->get('invoice_payment'); ?>
                                            <?php elseif($transaction->type == 'credit_usage'): ?>
                                                <?php echo $lang->get('invoice_payment_from_credit_balance'); ?>
                                            <?php elseif($transaction->type == 'debit'): ?>
                                                <?php echo $lang->get('debit_note'); ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo $lang->get('date_paid'); ?></strong></td>
                                        <td>
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $transaction->created_at,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo $lang->get('gateway'); ?></strong></td>
                                        <td>
                                            <?php $gateway = $transaction->Gateway()->first(); ?>
                                            <?php
                                            if ($gateway):
                                                echo $gateway->name;
                                            else:
                                                echo $lang->get('not_available');
                                            endif;
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo $lang->get('description'); ?></strong></td>
                                        <td><?php echo $transaction->description; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo $lang->get('total'); ?></strong></td>
                                        <td><?php echo App::get('money')->format($transaction->amount, $transaction->currency_id); ?></td>
                                    </tr>
                                    <?php if($transaction->invoice_id > 0): ?>
                                    <tr>
                                        <td><strong><?php echo $lang->get('invoice'); ?></strong></td>
                                        <td>
                                            <?php echo $invoice->invoice_no; ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <?php if($transaction->type != 'void' && $transaction->type != 'refunded'): ?>
                                <div class="text-center">
                                    <a href="<?php echo $router->generate('admin-client-void-transaction', array('id' => $client->id, 'transaction_id' => $transaction->id)); ?>" class="btn btn-danger"><?php echo $lang->get('void_transaction'); ?></a>
                                    <?php if($transaction->type != 'debit'): ?>
                                        <a href="<?php echo $router->generate('admin-client-refund-transaction', array('id' => $client->id, 'transaction_id' => $transaction->id)); ?>" class="btn btn-danger"><?php echo $lang->get('refund_transaction'); ?></a>
                                    <?php endif; ?>

                                    <?php if($transaction->invoice_id > 0): ?>
                                        <a href="<?php echo $router->generate('admin-client-remove-transaction-invoice', array('id' => $client->id, 'transaction_id' => $transaction->id)); ?>" class="btn btn-danger"><?php echo $lang->get('remove_from_invoice'); ?></a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php'); ?>
