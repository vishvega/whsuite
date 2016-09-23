<div class="row">
    <div class="col-md-6">
        <div class="well">
            <p><b><?php echo $lang->get('invoice_to'); ?>:</b></p>
            <p><?php echo $client->first_name; ?> <?php echo $client->last_name; ?></p>
            <p></p>
            <p>
                <?php echo $client->address1; ?><br>
                <?php if ($client->address2 != ''): ?>
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
    <div class="col-md-6 text-center">
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
            <a href="<?php echo $router->generate('admin-invoice-download', array('invoice_id' => $invoice->id)); ?>" class="btn btn-primary">
                <?php echo $lang->get('download_pdf'); ?>
            </a>
            <a href="<?php echo $router->generate('admin-invoice-email', array('invoice_id' => $invoice->id)); ?>" class="btn btn-primary"><?php echo $lang->get('email_to_client'); ?></a>

            <?php if($invoice->status < 2): ?>
                <a href="<?php echo $router->generate('admin-invoice-capture-payment', array('invoice_id' => $invoice->id)); ?>" class="btn btn-primary">
                    <?php echo $lang->get('capture_payment'); ?>
                </a>
                <a href="<?php echo $router->generate('admin-invoice-void', array('invoice_id' => $invoice->id)); ?>" class="btn btn-danger">
                    <?php echo $lang->get('void_invoice'); ?>
                </a>
            <?php else: ?>
                <a href="<?php echo $router->generate('admin-invoice-unvoid', array('invoice_id' => $invoice->id)); ?>" class="btn btn-warning">
                    <?php echo $lang->get('unvoid_invoice'); ?>
                </a>
            <?php endif; ?>
        </p>
    </div>
</div>
<?php echo $forms->open(array('method' => 'post', 'action' => $router->generate('admin-client-invoice-update', array('id' => $client->id, 'invoice_id' => $invoice->id)), 'class' => 'form-inline')); ?>
<table class="table">
    <thead>
        <tr>
            <th width="66%"><?php echo $lang->get('item'); ?></th>
            <th width="10%" class="text-center"><?php echo $lang->get('unit_price'); ?></th>
            <th width="8%" class="text-center"><?php echo $lang->get('taxable'); ?></th>
            <th width="8%"class="text-center"><?php echo $lang->get('edit'); ?></th>
            <th width="8%"class="text-center"><?php echo $lang->get('delete'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $item): ?>
        <tr>
            <td>
                <?php echo $forms->textarea('item.'.$item->id.'.description', false, array('rows' => '2')); ?>
            </td>
            <td>
                <?php echo $forms->input('item.'.$item->id.'.total', false, array('value' => App::get('money')->format($item->total, $currency->code, true))); ?>
            </td>
            <td class="text-center">
                <?php echo $forms->checkbox('item.'.$item->id.'.is_taxed', false); ?>
            </td>
            <td class="text-center">
                <a href="#" class="editItemButton btn btn-primary btn-small"><?php echo $lang->get('edit'); ?></a>
            </td>
            <td class="text-center"><?php echo $forms->checkbox('delete_item['.$item->id.']', false); ?></td>
        </tr>
        <tr class="hide editItem">
            <td colspan="5">
                <div class="row form-vertical">
                    <div class="col-lg-4">
                        <?php echo $forms->input('item['.$item->id.'][discount]', $lang->get('discount'), array('value' => App::get('money')->format($item->promotion_discount, $currency->code, true))); ?>
                    </div>
                    <div class="col-lg-4">
                        <?php echo $forms->select('item['.$item->id.'][discount_percentage]', $lang->get('discount_type'),
                            array(
                                'options' => array(
                                    '0' => $lang->get('fixed_amount'),
                                    '1' => $lang->get('percentage')
                                ),
                                'value' => $item->promotion_is_percentage
                            ));
                        ?>
                    </div>
                    <div class="col-lg-4">
                        <?php echo $forms->select('item['.$item->id.'][discount_before_tax]', $lang->get('discount_application'),
                            array(
                                'options' => array(
                                    '0' => $lang->get('after_tax'),
                                    '1' => $lang->get('before_tax')
                                ),
                                'value' => $item->prmotion_before_tax
                            ));
                        ?>
                    </div>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td><?php echo $forms->textarea('newitem[description]', false, array('rows' => '1')); ?></td>
            <td><?php echo $forms->input('newitem[total]', false); ?></td>
            <td class="text-center"><?php echo $forms->checkbox('newitem[is_taxed]', false); ?></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo $lang->get('subtotal'); ?>:</b> <?php echo App::get('money')->format($invoice->subtotal, $currency->code); ?>
            </td>
        </tr>
        <?php if ($invoice->pre_tax_discount > 0): ?>
        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo $lang->get('pre_tax_discount'); ?>:</b> -<?php echo App::get('money')->format($invoice->pre_tax_discount, $currency->code); ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if($invoice->level1_total > 0): ?>
        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo $lang->get('level_1_tax'); ?>:</b> <?php echo App::get('money')->format($invoice->level1_total, $currency->code); ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if($invoice->level2_total > 0): ?>
        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo $lang->get('level_2_tax'); ?>:</b> <?php echo App::get('money')->format($invoice->level2_total, $currency->code); ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if($invoice->post_tax_discount > 0): ?>
        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo $lang->get('post_tax_discount'); ?>:</b> -<?php echo App::get('money')->format($invoice->post_tax_discount, $currency->code); ?>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo $lang->get('total'); ?>:</b> <?php echo App::get('money')->format($invoice->total, $currency->code); ?>
            </td>
        </tr>
        <tr>
            <td colspan="5" class="text-right">
                <b><?php echo $lang->get('total_paid'); ?>:</b> <?php echo App::get('money')->format($invoice->total_paid, $currency->code); ?>
            </td>
        </tr>
        <?php if (($invoice->total-$invoice->total_paid) > 0): ?>
            <tr>
                <td colspan="5" class="text-right">
                    <b><?php echo $lang->get('total_remainin_due'); ?>:</b> <?php echo App::get('money')->format(($invoice->total-$invoice->total_paid), $currency->code); ?>
                </td>
            </tr>
        <?php endif; ?>
    </tfoot>
</table>
<p class="text-center">
    <?php echo $forms->submit('save', $lang->get('update_invoice')); ?>
</p>
<?php echo $forms->close(); ?>
<hr>

<h3><?php echo $lang->get('applied_transactions'); ?></h3>
<?php if(count($transactions) > 0): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th data-hide="phone,tablet"><?php echo $lang->get('transaction_id'); ?></th>
                <th><?php echo $lang->get('date'); ?></th>
                <th data-hide="phone"><?php echo $lang->get('amount'); ?></th>
                <th data-hide="phone"><?php echo $lang->get('client'); ?></th>
                <th data-hide="phone"><?php echo $lang->get('status'); ?></th>
                <th data-hide="phone,tablet"><?php echo $lang->get('description'); ?></th>
                <th><?php echo $lang->get('delete'); ?></th>
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
                <td><?php echo App::get('money')->format($transaction->amount, $transaction->Currency->code);?></td>
                <td>
                    <a href="<?php echo $router->generate('admin-client-profile', array('id' => $transaction->Client->id)); ?>">
                        <?php echo $transaction->Client->first_name; ?> <?php echo $transaction->Client->last_name; ?>
                    </a>
                </td>
                <td>
                    <?php
                        echo \App\Libraries\Transactions::formatTransactionType($transaction->type);
                    ?>
                </td>
                <td><?php echo $transaction->description; ?></td>
                <td>
                    <a href="<?php echo $router->generate('admin-client-manage-transaction', array('id' => $transaction->Client->id, 'transaction_id' => $transaction->id)); ?>" class="btn btn-primary">
                        <?php echo $lang->get('manage'); ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info"><?php echo $lang->get('no_transactions_applied'); ?></div>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $('.editItemButton').on('click', function(e) {
            e.preventDefault();

            if ($(this).parent().parent().next('.editItem').is(":visible")) {
                $(this).parent().parent().next('.editItem').addClass('hide');
            } else {
                $(this).parent().parent().next('.editItem').removeClass('hide').slideDown();
            }
        });
    });
</script>
