<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang->get('date'); ?></th>
                            <th><?php echo $lang->get('status'); ?></th>
                            <th><?php echo $lang->get('amount'); ?></th>
                            <th><?php echo $lang->get('description'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($transactions) > 0): ?>
                            <?php foreach($transactions as $transaction): ?>
                                <tr>
                                    <td>
                                        <?php
                                            $Carbon = \Carbon\Carbon::parse(
                                                $transaction->created_at,
                                                $date['timezone']
                                            );
                                            echo $Carbon->format($date['short_datetime']);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            echo \App\Libraries\Transactions::formatTransactionType($transaction->type);
                                        ?>
                                    </td>
                                    <td><?php echo App::get('money')->format($transaction->amount, $transaction->currency_id, false, true); ?></td>
                                    <td><?php echo $transaction->description; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <?php echo $lang->get('no_results_found'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">
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
