<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang->get('invoice_no'); ?></th>
                            <th class="text-center"><?php echo $lang->get('date_created'); ?></th>
                            <th class="text-center"><?php echo $lang->get('date_due'); ?></th>
                            <th class="text-center"><?php echo $lang->get('total'); ?></th>
                            <th class="text-center"><?php echo $lang->get('status'); ?></th>
                            <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($invoices) > 0): ?>
                            <?php foreach($invoices as $invoice): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo $router->generate('client-manage-invoice', array('id' => $invoice->id)); ?>">
                                            <?php echo $lang->get('invoice'); ?> #<?php echo $invoice->invoice_no; ?>
                                        </a>
                                    </td>
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
                                            $Carbon = \Carbon\Carbon::parse(
                                                $invoice->date_due,
                                                $date['timezone']
                                            );
                                            echo $Carbon->format($date['short_date']);
                                        ?>
                                    </td>
                                    <td class="text-center"><?php echo App::get('money')->format($invoice->total, $invoice->currency_id, false, true); ?></td>
                                    <td class="text-center">
                                        <?php if ($invoice->status == '1'): ?>
                                            <span class="label label-success"><?php echo $lang->get('paid'); ?></span>
                                        <?php elseif ($invoice->status == '2'): ?>
                                            <span class="label label-default"><?php echo $lang->get('void'); ?></span>
                                        <?php else: ?>
                                            <span class="label label-danger"><?php echo $lang->get('unpaid'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?php echo $router->generate('client-manage-invoice', array('id' => $invoice->id)); ?>" class="btn btn-primary btn-xs">
                                            <?php echo $lang->get('manage'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <?php echo $lang->get('no_results_found'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">
                                <?php echo $pagination; ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
