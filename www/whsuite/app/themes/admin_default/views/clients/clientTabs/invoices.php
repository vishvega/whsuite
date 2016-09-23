<div class="panel panel-secondary">
    <div class="panel-heading"><?php echo $lang->get('invoices'); ?></div>
    <div class="panel-content">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo $lang->get('invoice_no'); ?></th>
                    <th data-hide="all"><?php echo $lang->get('date_created'); ?></th>
                    <th data-hide="all"><?php echo $lang->get('date_due'); ?></th>
                    <th data-hide="all"><?php echo $lang->get('date_paid'); ?></th>
                    <th data-hide="phone"><?php echo $lang->get('total_due'); ?></th>
                    <th data-hide="phone"><?php echo $lang->get('total_paid'); ?></th>
                    <th><?php echo $lang->get('status'); ?></th>
                    <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($invoices->count() > 0): ?>
                <?php foreach ($invoices as $invoice):
                    $currency = $invoice->Currency()->first();
                ?>
                <tr>
                    <td><?php echo $invoice->invoice_no; ?></td>
                    <td>
                        <?php
                            $Carbon = \Carbon\Carbon::parse(
                                $invoice->created_at,
                                $date['timezone']
                            );
                            echo $Carbon->format($date['short_date']);
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($invoice->date_due):
                            $Carbon = \Carbon\Carbon::parse(
                                $invoice->date_due,
                                $date['timezone']
                            );
                            echo $Carbon->format($date['short_date']);
                        else:
                            echo $lang->get('not_available');
                        endif;
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
                    <td><?php echo App::get('money')->format($invoice->total_paid, $currency->code); ?></td>
                    <td>
                        <?php if ($invoice->status == '1'): ?>
                            <span class="label label-success"><?php echo $lang->get('paid'); ?></span>
                        <?php elseif ($invoice->status == '2'): ?>
                            <span class="label label-default"><?php echo $lang->get('void'); ?></span>
                        <?php else: ?>
                            <span class="label label-danger"><?php echo $lang->get('unpaid'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <a href="<?php echo $router->generate('admin-client-invoice', array('id' => $client->id, 'invoice_id' => $invoice->id)); ?>" class="btn btn-primary btn-small">
                            <?php echo $lang->get('manage'); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="<?php echo $router->generate('admin-client-new-invoice', array('id' => $client->id)); ?>" class="btn btn-small btn-primary">
                                    <i class="fa fa-plus"></i> <?php echo $lang->get('new_invoice'); ?>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <a href="<?php echo $router->generate('admin-client-invoices', array('id' => $client->id)); ?>" class="btn btn-small btn-primary">
                                    <i class="fa fa-list"></i> <?php echo $lang->get('all_invoices'); ?>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
