<!-- This is the default PDF Invoice style. You can edit this as you wish, however should
    remember that PDF's are limited in what they support. We do not recommend using advanced
    CSS techniques. External styles are not supported Always test your changes by generating a PDF. -->
<html>
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style>
        *{
            font-family: 'DejaVu Sans', sans-serif !important;
            font-size: 12px;
            line-height: 16px;
        }

        .tbl td
        {
            padding: 5px;
        }

        .tbl tr.highlight td
        {
            background: #EEEEEE;
            border-top: 1px solid #DDDDDD;

        }
        .tbl-top-margin { margin-top: 30px; }

        .hr
        {
            height: 1px;
            width: 100%;
            border-bottom: 1px solid #DDDDDD;
            margin-top: 20px;
        }

        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .header
        {
            background: #EEEEEE;
            line-height: 25px;
            height: 25px;
            padding: 10px;
        }

        .h1
        {
            font-size: 28px;
            font-weight: bold;
        }

        .spacer{ height: 10px; }
        .invoice_tbl
        {
            background: #DDDDDD;
        }
        .invoice_tbl thead th
        {
            background: #EEEEEE;
        }

        .invoice_tbl tbody td
        {
            background: #FFFFFF
        }

        .invoice_tbl tfoot td
        {
            background: #EEEEEE;
        }

        .subheader
        {
            font-size: 18px;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .generated
        {
            text-align: center;
            color: #777777;
            margin-top: 10px;
        }

        .alert
        {
            text-align: center;
            background: #CC0000;
            color: #FFFFFF;
            padding: 5px;
            width: 100%;
            font-weight: bold;
        }

        .paystate
        {
            transform: rotate(5deg);
            z-index: 1000;
            position: absolute;
            top: -30;
            right: -30;
            font-size: 50px;
            text-transform: uppercase;
            background: #eee;
            height:55px;
            line-height: 55px;
            padding: 20px;
            text-align:center;
            opacity: 0.3;
            border-radius:20px;
        }

        .paid
        {
            background: #99FF00;
        }

        .unpaid
        {
            background: #CC0000;
        }

    </style>
</head>
<body>

    <?php if($invoice->total_paid >= $invoice->total): ?>
        <div class="paystate paid"><?php echo $lang->get('paid'); ?></div>
    <?php else: ?>
        <div class="paystate unpaid"><?php echo $lang->get('unpaid'); ?></div>
    <?php endif; ?>

    <table class="header" width="100%">
        <tr>
            <td class="h1"><?php echo $lang->get('invoice_no'); ?> #<?php echo $invoice->invoice_no; ?></td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td width="50%" valign="top">
                <img src="<?php echo $view->getThemeDir().'/'.$view->getTheme().'/assets/img/pdf_logo.jpg'; ?>" align="left">
            </td>
            <td width="50%" align="right"><?php echo nl2br(App::get('configs')->get('settings.mail.invoice_from')); ?></td>
        </tr>
        <tr>
            <td width="50%">
                <b><?php echo $lang->get('invoice_to'); ?>:</b><br>
                <?php echo $client->first_name; ?> <?php echo $client->last_name; ?><br>
                <?php if($client->company !=''):?>
                    <?php echo $client->company; ?><br>
                <?php endif; ?>
                <?php echo $client->address1; ?><br>
                <?php if ($client->address2 != ''): ?>
                    <?php echo $client->address2; ?><br>
                <?php endif; ?>
                <?php echo $client->city; ?><br>
                <?php echo $client->state; ?><br>
                <?php echo $client->postcode; ?><br>
                <?php echo $client->country; ?><br>
                <b><?php echo $lang->get('email'); ?>:</b> <?php echo $client->email; ?><br>
                <b><?php echo $lang->get('telephone'); ?>:</b> <?php echo $client->phone; ?>
            </td>
            <td width="50%" align="right" valign="bottom">
                <table class="tbl tbl-top-margin" width="80%" align="right">
                    <tr>
                        <td><b><?php echo $lang->get('date_created'); ?>:</b></td>
                        <td align="right">
                            <?php
                                $Carbon = \Carbon\Carbon::parse(
                                    $invoice->created_at,
                                    $date['timezone']
                                );
                                echo $Carbon->format($date['short_date']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b><?php echo $lang->get('date_due'); ?>:</b></td>
                        <td align="right">
                            <?php
                                $Carbon = \Carbon\Carbon::parse(
                                    $invoice->date_due,
                                    $date['timezone']
                                );
                                echo $Carbon->format($date['short_date']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b><?php echo $lang->get('total_due'); ?>:</b></td>
                        <td align="right"><?php echo App::get('money')->format(($invoice->total - $invoice->total_paid), $currency->code); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="spacer"></div>
    <div class="hr"></div>
    <div class="spacer"></div>

    <table class="invoice_tbl" width="100%" cellpadding="5">
        <thead>
            <tr>
                <th width="80%" class="text-left"><?php echo $lang->get('item'); ?></th>
                <th width="20%" class="text-right"><?php echo $lang->get('unit_price'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item): ?>
            <tr>
                <td><?php echo $item->description; ?></td>
                <td class="text-right"><?php echo App::get('money')->format($item->total, $currency->code); ?> (<?php echo $currency->code; ?>)</td>
            </tr>
            <?php endforeach; ?>
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

    <div class="spacer"></div>
    <div class="hr"></div>
    <div class="spacer"></div>

    <div class="subheader"><?php echo $lang->get('applied_transactions'); ?></div>
    <?php if(count($transactions) > 0): ?>
    <table class="invoice_tbl" width="100%" cellpadding="5">
        <thead>
            <tr>
                <th><?php echo $lang->get('date'); ?></th>
                <th><?php echo $lang->get('status'); ?></th>
                <th><?php echo $lang->get('amount'); ?></th>
                <th><?php echo $lang->get('description'); ?></th>
            </tr>
        </thead>
        <tbody>
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
                <td>
                    <?php
                        echo \App\Libraries\Transactions::formatTransactionType($transaction->type, false);
                    ?>
                </td>
                <td><?php echo App::get('money')->format($transaction->amount, $transaction->Currency->code);?></td>
                <td><?php echo $transaction->description; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="alert"><?php echo $lang->get('no_transactions_applied'); ?></div>
    <?php endif; ?>

    <div class="spacer"></div>
    <div class="hr"></div>
    <div class="spacer"></div>

    <div class="generated"><?php echo $lang->get('pdf_generated'); ?> <?php echo date($date['short_date']); ?></div>
</body>
</html>