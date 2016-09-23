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
                        <div class="panel-heading"><?php echo $lang->get('invoices'); ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('invoice_no'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('client'); ?></th>
                                        <th><?php echo $lang->get('date_due'); ?></th>
                                        <th><?php echo $lang->get('date_paid'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('total_due'); ?></th>
                                        <th><?php echo $lang->get('status'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('manage'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($invoices as $invoice):
                                        $currency = $invoice->Currency()->first();
                                        $client = $invoice->Client()->first();
                                    ?>
                                    <tr>
                                        <td><a href="<?php echo $router->generate('admin-client-invoice', array('id' => $client->id, 'invoice_id' => $invoice->id)); ?>"><?php echo $invoice->invoice_no; ?></a></td>
                                        <td><a href="<?php echo $router->generate('admin-client-profile', array('id' => $client->id)); ?>"><?php echo $client->first_name; ?> <?php echo $client->last_name; ?></td>
                                        <td>
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $invoice->date_due,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($invoice->date_paid > '1970-01-01 00:00:00'):
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $invoice->date_paid,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            else:
                                                echo $lang->get('not_available');
                                            endif;
                                            ?>
                                        </td>
                                        <td><?php echo App::get('money')->format($invoice->total, $currency->code); ?></td>
                                        <td>
                                            <?php if ($invoice->status == '1'): ?>
                                                <span class="label label-success"><?php echo $lang->get('paid'); ?></span>
                                            <?php elseif ($invoice->status == '2'): ?>
                                                <span class="label label-default"><?php echo $lang->get('void'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-danger"><?php echo $lang->get('unpaid'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client-invoice', array('id' => $client->id, 'invoice_id' => $invoice->id)); ?>"><?php echo $lang->get('manage'); ?></a> /
                                            <a href="<?php echo $router->generate('admin-invoice-download', array('invoice_id' => $invoice->id)); ?>"><?php echo $lang->get('pdf'); ?></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7" class="text-right"><?php echo $pagination; ?></td>
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
