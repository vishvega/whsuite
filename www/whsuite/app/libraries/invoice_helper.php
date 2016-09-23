<?php

namespace App\Libraries;

use \Illuminate\Support\Str;

class InvoiceHelper
{
    public function emailInvoice($invoice_id)
    {
        $invoice = \Invoice::find($invoice_id);
        if (empty($invoice)) {
            return false;
        }

        $client = $invoice->Client()->first();
        $currency = $invoice->Currency()->first();

        $attachments = array(
            array(
                'filename' => 'invoice-'.$invoice->invoice_no.'.pdf',
                'mime_type' => 'application/pdf',
                'type' => 'data',
                'data' => $this->generateInvoicePdf($invoice->id, true)
            )
        );

        $CarbonDue = \Carbon\Carbon::parse(
            $invoice->date_due,
            \App::get('configs')->get('settings.localization.timezone')
        );
        $CarbonCreate = \Carbon\Carbon::parse(
            $invoice->created_at,
            \App::get('configs')->get('settings.localization.timezone')
        );

        $data = array(
            'client' => $client,
            'currency' => $currency,
            'invoice' => array(
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'date_due' => $CarbonDue->format(\App::get('configs')->get('settings.localization.short_date_format')),
                'created_at' => $CarbonCreate->format(\App::get('configs')->get('settings.localization.short_date_format')),
                'total' => \App::get('money')->format($invoice->total, $currency->code),
                'total_paid' => \App::get('money')->format($invoice->total_paid, $currency->code),
            )
        );

        // All ok, now time to send the email, and insert a copy of it into the client's email log.
        if (\App::get('email')->sendTemplateToClient($invoice->client_id, 'client_invoice', $data, $attachments)) {
            return true;
        }

        return false;
    }


    public function generateInvoicePdf($invoice_id, $raw = false)
    {
        $invoice = \Invoice::find($invoice_id);
        if (empty($invoice)) {
            return false;
        }

        $pdf = new \App\Libraries\Pdf();

        $transactions = $invoice->Transaction()
            ->with(array(
                'Currency', 'Client'
            ))
            ->get();

        \App::get('view')->set('invoice', $invoice);
        \App::get('view')->set('items', $invoice->InvoiceItem()->get());
        \App::get('view')->set('client', $invoice->Client()->first());
        \App::get('view')->set('currency', $invoice->Currency()->first());
        \App::get('view')->set('transactions', $transactions);
        $pdf->load_html(\App::get('view')->fetch('invoices/pdf/invoice.php'), 'UTF-8');
        $pdf->render();

        if ($raw) {
            return $pdf->output();
        }

        return $pdf->stream('invoice-'.$invoice->invoice_no.'.pdf', array('Attachment' => 0));
    }

    /**
     * update and recalculate all the invoice totals
     * and work out how much has / has not been paid
     *
     * @param   Int     Invoice ID
     * @return  bool
     */
    public function updateInvoice($invoice_id)
    {
        $invoice = \Invoice::find($invoice_id);
        if (empty($invoice)) {
            return false;
        }

        $client = $invoice->Client()->first();

        // For the first part of the invoice update, we completely recalculate
        // the totals, including taxes, promotions, etc.

        // Setup some vars that we'll be storing our totals in
        $subtotal = 0;
        $tax_level1_total = 0;
        $tax_level2_total = 0;
        $total = 0;
        $total_paid = 0;

        $pre_tax_discounts = 0;
        $post_tax_discounts = 0;

        foreach ($invoice->InvoiceItem()->get() as $item) {
            // Create empty subtotals just for this item. This is done as we may have a discount that needs
            // to be applied before we add it to the main subtotal.
            $item_subtotal = 0;
            $item_level1_total = 0;
            $item_level2_total = 0;

            // Firstly, lets go ahead and add the item's total to its subtotal.
            $item_subtotal = $item->total;

            // Now we need to see if there is any discount being applied BEFORE tax.
            if ($item->promotion_discount > 0 && $item->promotion_before_tax == '1') {
                // Ok a discount exists on this item, and it needs to be done before we add tax.

                // Lets check if its a fixed-amount, or a percentage, then go ahead and apply the discount.
                if ($item->promotion_is_percentage == '1') {
                    // It's a percentage, so work it out and deduct it from the item subtotal.
                    $discount = ($item->promotion_discount / 100) * $item_subtotal;
                    $pre_tax_discounts = $pre_tax_discounts + $discount;

                } else {
                    // It's a fixed amount, so just deduct it from the item subtotal.
                    $pre_tax_discounts = $pre_tax_discounts + $item->promotion_discount;
                }
            }

            if ($item->is_taxed == '1' && $client->is_taxexempt == '0') {
                // Item is taxable, so work out how much tax to charge.

                // First lets work out how much Level 1 tax is applicable
                $item_level1_total = (($invoice->level1_rate / 100) * $item_subtotal);

                // Now lets work out how much Level 2 tax is applicable
                $item_level2_total = (($invoice->level2_rate / 100) * $item_subtotal);

                $tax_level1_total = $tax_level1_total + $item_level1_total;
                $tax_level2_total = $tax_level2_total + $item_level2_total;
            }

            // Now we need to do all that promotion stuff again for any promotions that were to be applied after tax.
            if ($item->promotion_discount > 0 && $item->promotion_before_tax == '0') {
                // Ok a discount exists on this item, and it needs to be done before we add tax.

                $item_taxed_subtotal = $item_subtotal + $item_level1_total + $item_level2_total;

                // Lets check if its a fixed-amount, or a percentage, then go ahead and apply the discount.
                if ($item->promotion_is_percentage == '1') {
                    // It's a percentage, so work it out and deduct it from the item taxed subtotal.
                    $discount = ($item->promotion_discount / 100) * $item_taxed_subtotal;
                    $post_tax_discounts = $post_tax_discounts + $discount;

                } else {
                    // It's a fixed amount, so just deduct it from the item subtotal.
                    $post_tax_discounts = $post_tax_discounts + $item->promotion_discount;
                }
            }

            // Discounts (if any) are now all applied, so we can move the item subtotal into the main subtotal.
            $subtotal = $subtotal + $item_subtotal;
        }

        // Lets now work out how much has been paid towards the invoice
        $transactions = \Transaction::where('invoice_id', '=', $invoice->id)
            ->get();
        $paidTypes = array('credit_usage', 'invoice');

        foreach ($transactions as $transaction) {
            if (in_array($transaction->type, $paidTypes)) {
                $total_paid += $transaction->amount;
            }
        }

        // Now that al the items have been looped through, we know the total tax
        // that needs adding to the total, so lets go ahead and do that now.
        $total = $subtotal + $tax_level1_total + $tax_level2_total;
        // That was easy :) Now lets update the totals in the invoices table to finish off.
        $invoice->subtotal = $subtotal;
        $invoice->level1_total = $tax_level1_total;
        $invoice->level2_total = $tax_level2_total;
        $invoice->total = $total - $pre_tax_discounts - $post_tax_discounts;
        $invoice->total_paid = $total_paid;

        if ($invoice->total_paid >= $invoice->total || $invoice->total == 0) {
            // The invoice has been paid in full. We need to update the status
            // to reflect that.
            $invoice->status = '1';
            $invoice->date_paid = date('Y-m-d');

            // if total paid is greater then the total, issue account credit
            if ($invoice->total_paid > $invoice->total) {
                $overPayment = $invoice->total_paid - $invoice->total;

                $receipt = new \Transaction;
                $receipt->client_id = $client->id;
                $receipt->gateway_id = null;
                $receipt->currency_id = $invoice->currency_id;
                $receipt->description = \App::get('translation')->get('invoice_overpayment_account_credit');
                $receipt->type = 'receipt';
                $receipt->amount = $overPayment;
                $receipt->invoice_id = $invoice->id;

                if ($receipt->save()) {
                    // receipt added - remove overpayment from total paid
                    $invoice->total_paid -= $overPayment;
                }
            }

        } else {
            $invoice->status = '0';
            $invoice->date_paid = null;
        }

        $invoice->pre_tax_discount = $pre_tax_discounts;
        $invoice->post_tax_discount = $post_tax_discounts;

        if ($invoice->save()) {
            if ($invoice->status == '1') {
                // The invoice is marked as paid - we now want to go through the
                // items and perform actions to update them. This is used for things
                // like setting the next invoice/renewal date of a purchase and
                // unsuspending services if they were suspended for being overdue.

                // For usefulness we'll have a hook here that can be called. This
                // should help with any addon development.
                \App::get('hooks')->callListeners('invoice-paid', $invoice);

                $order_helper = \App::factory('\App\Libraries\OrderHelper');
                $purchase_helper = \App::factory('\App\Libraries\PurchaseHelper');

                $order = $invoice->Order()->first();

                if (! empty($order)) {
                    // New order
                    $order_helper->activateOrder($order->id);
                } else {
                    // Existing order
                    foreach ($invoice->InvoiceItem()->get() as $item) {
                        if ($item->product_purchase_id > 0) {
                            $purchase_helper->processPurchase($item->product_purchase_id);
                        }
                    }
                }
            }
            \App::get('hooks')->callListeners('invoice-updated', $invoice);
            return true;
        }

        // If we get to this point, the invoice wasn't saved, so return false.
        return false;
    }

    /**
     * attempt a payment on an invoice
     *
     * @param   int     $invoice_id     The invoice Id number
     * @return  bool    $paid           True if payment was successfully taken
     */
    public function attemptPayment($invoice_id)
    {
        $invoice = \Invoice::find($invoice_id);
        if (empty($invoice)) {
            return false;
        }

        $client = $invoice->Client()->first();
        $client_cc = $client->ClientCc()
            ->where('is_active', '=', '1')
            ->where('currency_id', '=', $invoice->currency_id)
            ->orderBy('is_default', 'desc')
            ->get();
        $client_ach = $client->ClientAch()
            ->where('is_active', '=', '1')
            ->where('currency_id', '=', $invoice->currency_id)
            ->orderBy('is_default', 'desc')
            ->get();
        $currency = $invoice->Currency()->first();

        // check if there is actually anything to pay on the invoice
        $totalDue = ($invoice->total - $invoice->total_paid);
        if ($totalDue == 0) {
            // trigger update to trigger invoice-paid hook
            $transaction = array(
                'client_id' => $client->id,
                'gateway_id' => 0,
                'invoice_id' => $invoice->id,
                'currency_id' => $invoice->currency_id,
                'description' => \App::get('translation')->get('paid'),
                'type' => 'invoice',
                'amount' => 0
            );
            \App\Libraries\Payments::createTransaction($transaction);

            $this->updateInvoice($invoice->id);
            return true;
        }

        // Set a paid variable so that if we end up paying this with a CC, we wont
        // then try to also pay it with an ACH.
        $paid = false;

        // TODO: Add a check to pay the invoice using client credit in full or part first.
        $accountCredit = \App\Libraries\Transactions::clientCredit($client->id, $invoice->currency_id, true, false);

        if ($accountCredit > 0) {
            $totalDue = ($invoice->total - $invoice->total_paid);

            if ($accountCredit >= $totalDue) {
                $amountToPay = $totalDue;
                $paid = true;
            } else {
                $amountToPay = $accountCredit;
            }

            $transaction = array(
                'client_id' => $client->id,
                'gateway_id' => 0,
                'invoice_id' => $invoice->id,
                'currency_id' => $invoice->currency_id,
                'description' => \App::get('translation')->get('invoice_payment_from_credit_balance'),
                'type' => 'credit_usage',
                'amount' => $amountToPay
            );

            \App\Libraries\Payments::createTransaction($transaction);

            // We need to send the client an email confirming
            // that payment has been taken.
            $data = array(
                'invoice' => $invoice,
                'client' => $client,
                'transaction' => $transaction,
                'currency' => $currency,
                'total_payment' => \App::get('money')->format($amountToPay, $currency->code).' '.$currency->code
            );
            \App::get('email')->sendTemplateToClient($invoice->client_id, 'client_account_credit_payment_successful', $data);

            // The transaction worked - we do now need to update
            // the invoice so that if its now paid, we mark the
            // invoice as paid, and if necessary, perform any
            // actions on the invoice items.
            $this->updateInvoice($invoice->id);

            if (! $paid) {
                $invoice = \Invoice::find($invoice->id);
            }
        }

        // Credit/debit cards are checked first - these take priority over
        // ACH as they are generally faster, and more likely to be a preferred
        // payment method.

        if ($client_cc->count() > 0 && ! $paid) {
            // Client has one or more credit/debit cards on file.
            foreach ($client_cc as $cc) {
                // Decrypt the expiry date and last 4 digits
                $cc->account_last4 = \App::get('security')->decrypt($cc->account_last4);
                $cc->account_expiry = \App::get('security')->decrypt($cc->account_expiry);

                if (strlen($cc->account_expiry) != 4 || !is_numeric($cc->account_expiry)) {
                    continue;
                }

                // Split the month and year
                $expiry_month = substr($cc->account_expiry, 0, 2);
                $expiry_year = substr($cc->account_expiry, -2, 2);

                //$expiry_timestamp = mktime('23', '59', '59', $expiry_month, date('t'), $expiry_year);
                // Put that into a standard date format with it set to
                // the 1st of the month
                $expiry_date = $expiry_year . '-' . $expiry_month . '-' . '01';

                // Work out the expiry timestamp based on the above. This
                // basically creates a timestamp, set to 1 second past
                // the expiry date. So if a card expires at 23:59:59 on
                // December 31 2020 this value will be for 12:00:00 am
                // on January 1, 2021.
                $expiry_timestamp = strtotime("+1 month", strtotime($expiry_date));
                if ($expiry_timestamp < time()) {
                    // The card has expired and cant be used.
                    continue;
                }

                // At this point we know the card is valid. We dont know
                // however what gateway we're using, or if that gateway is
                // even active. Lets do that check now.
                if ($cc->gateway_id > 0) {
                    $gateway = $cc->Gateway()->first();

                    if ($gateway && $gateway->is_active == '1') {
                        // Now that we've loaded the gateway and it's valid
                        // we need to check and see if it has an addon. If
                        // it does not have an addon, it's likely something
                        // like an offline payment gateway. If this is the
                        // case we dont need to do anything more, as we cant
                        // try to automate a manual payment method.
                        $addon = $gateway->Addon()->first();
                        if ($addon) {
                            // Great, we found an addon - that means we can
                            // get it to process the card.
                            $addonCameled = Str::studly($addon->directory);

                            $ccAttemptPayment = \App::factory('\\Addon\\' . $addonCameled . '\Libraries\\' . $addonCameled . 'Cc')
                                ->attemptInvoiceCcPayment($cc, $invoice);

                            if ($ccAttemptPayment) {
                                // The payment was successfully applied.
                                // create a transaction record of the payment.
                                $transaction = array(
                                    'client_id' => $client->id,
                                    'gateway_id' => $gateway->id,
                                    'invoice_id' => $invoice->id,
                                    'currency_id' => $invoice->currency_id,
                                    'description' => \App::get('translation')->get('cc_payment_via_gateway').' '.$gateway->name,
                                    'type' => 'invoice',
                                    'amount' => ($invoice->total - $invoice->total_paid)
                                );

                                \App\Libraries\Payments::createTransaction($transaction);

                                // We need to send the client an email confirming
                                // that payment has been taken.
                                $data = array(
                                    'invoice' => $invoice,
                                    'client' => $client,
                                    'transaction' => $transaction,
                                    'cc' => $cc,
                                    'currency' => $currency,
                                    'total_payment' => \App::get('money')->format(($invoice->total - $invoice->total_paid), $currency->code).' '.$currency->code
                                );
                                \App::get('email')->sendTemplateToClient($invoice->client_id, 'client_cc_payment_successful', $data);

                                // The transaction worked - we do now need to update
                                // the invoice so that if its now paid, we mark the
                                // invoice as paid, and if necessary, perform any
                                // actions on the invoice items.
                                $this->updateInvoice($invoice_id);

                                $paid = true;

                                break;
                            } else {
                                // The cc payment failed, so email the client to
                                // inform them. We'll then continue to see if any
                                // other stored CC or ACH will work for this client.
                                $data = array(
                                    'invoice' => $invoice,
                                    'client' => $client,
                                    'cc' => $cc,
                                    'currency' => $currency,
                                    'total_payment' => \App::get('money')->format(($invoice->total - $invoice->total_paid), $currency->code).' '.$currency->code
                                );
                                \App::get('email')->sendTemplateToClient($invoice->client_id, 'client_cc_payment_failed', $data);
                                continue;
                            }
                        }
                    }
                }
            }
        }

        if ($client_ach->count() > 0 && ! $paid) {
            // Client has one or more ACH accounts on file.
            foreach ($client_ach as $ach) {
                // Decrypt the expiry date and last 4 digits
                $ach->account_last4 = \App::get('security')->decrypt($ach->account_last4);

                // Check for a gateway for this ACH account
                if ($ach->gateway_id > 0) {
                    $gateway = $ach->Gateway()->first();

                    if ($gateway && $gateway->is_active == '1') {
                        // Now that we've loaded the gateway and it's valid
                        // we need to check and see if it has an addon. If
                        // it does not have an addon, it's likely something
                        // like an offline payment gateway. If this is the
                        // case we dont need to do anything more, as we cant
                        // try to automate a manual payment method.
                        $addon = $gateway->Addon()->first();

                        if ($addon) {
                            // Great, we found an addon - that means we can
                            // get it to process the ach details.
                            $addonCameled = Str::studly($addon->directory);

                            $achAttemptPayment = \App::factory('\\Addon\\' . $addonCameled . '\Libraries\\' . $addonCameled . 'Ach')
                                ->attemptInvoiceAchPayment($ach, $invoice);

                            if ($achAttemptPayment) {
                                // The payment was successfully applied.
                                // create a transaction record of the payment.
                                $transaction = array(
                                    'client_id' => $client->id,
                                    'gateway_id' => $gateway->id,
                                    'invoice_id' => $invoice->id,
                                    'currency_id' => $invoice->currency_id,
                                    'description' => \App::get('translation')->get('ach_payment_via_gateway').' '.$gateway->name,
                                    'type' => 'invoice',
                                    'amount' => ($invoice->total - $invoice->total_paid)
                                );

                                \App\Libraries\Payments::createTransaction($transaction);

                                // We need to send the client an email confirming
                                // that payment has been taken.
                                $data = array(
                                    'invoice' => $invoice,
                                    'client' => $client,
                                    'transaction' => $transaction,
                                    'ach' => $ach,
                                    'currency' => $currency,
                                    'total_payment' => \App::get('money')->format(($invoice->total - $invoice->total_paid), $currency->code).' '.$currency->code
                                );
                                \App::get('email')->sendTemplateToClient($invoice->client_id, 'client_ach_payment_successful', $data);

                                // The transaction worked - we do now need to update
                                // the invoice so that if its now paid, we mark the
                                // invoice as paid, and if necessary, perform any
                                // actions on the invoice items.
                                $this->updateInvoice($invoice_id);

                                $paid = true;

                                // Now we end the loop.
                                break;
                            } else {
                                // The payment failed so lets send the client an
                                // email to let them know. We'll then try any other
                                // cards they own that are on the system.
                                $data = array(
                                    'invoice' => $invoice,
                                    'client' => $client,
                                    'ach' => $ach,
                                    'currency' => $currency,
                                    'total_payment' => \App::get('money')->format(($invoice->total - $invoice->total_paid), $currency->code).' '.$currency->code
                                );
                                \App::get('email')->sendTemplateToClient($invoice->client_id, 'client_ach_payment_failed', $data);
                                continue;
                            }
                        }
                    }
                }
            }
        }

        return $paid;
    }

    /**
     * Manual Payment
     *
     * This is used for manual (i.e user selected) invoice payments, as it allows
     * the user to select the payment method, and supports both stored CC/ACH accounts
     * as well as manually entered CC/ACH accounts and other non-merchant gateways.
     *
     * @param  integer $invoice_id  Invoice ID to pay
     * @param  string $method_type Payment method type (stored_cc, stored_ach, cc, ach, gateway_<name>)
     * @param  array  $data        [description]
     * @return bool              [description]
     */
    public function manualPayment($invoice_id, $method_type, $data = array())
    {
        $invoice = \Invoice::find($invoice_id);
        if (empty($invoice)) {
            return false;
        }

        $client = $invoice->Client()->first();
        $currency = $invoice->Currency()->first();

        $total_due = $invoice->total - $invoice->total_paid;
        // check if there is actually anything to pay on the invoice
        if ($total_due == 0) {
            // trigger update to trigger invoice-paid hook
            $transaction = array(
                'client_id' => $client->id,
                'gateway_id' => 0,
                'invoice_id' => $invoice->id,
                'currency_id' => $invoice->currency_id,
                'description' => \App::get('translation')->get('paid'),
                'type' => 'invoice',
                'amount' => 0
            );
            \App\Libraries\Payments::createTransaction($transaction);

            \App::get('session')->setFlash('success', \App::get('translation')->get('payment_successfully_taken'));
            $this->updateInvoice($invoice->id);
            return true;
        }

        if ($method_type == 'stored_cc') {
            $cc_explode = explode('cc_', $data['account']);
            $cc_id = $cc_explode[1];

            $cc = \ClientCc::find($cc_id);

            if (
                $invoice->client_id === $cc->client_id &&
                $cc->is_active == '1' &&
                $invoice->currency_id === $cc->currency_id
            ) {
                // The credit card is valid and belongs to this client.

                $gateway = $cc->Gateway()->first();
                $gateway_currency = \GatewayCurrency::where('gateway_id', '=', $gateway->id)->where('currency_id', '=', $currency->id)->first();
                if (! empty($gateway) && $gateway->is_merchant == '1' && ! empty($gateway_currency)) {
                    // We found the gateway, and it's a merchant so we're good to go!
                    // Load up the merchant gateway details to see if it accepts storage
                    // of cc accounts.
                    $addon = $gateway->addon()->first();
                    $addonCameled = Str::studly($addon->directory);

                    $details_class_name = '\\Addon\\' . $addonCameled . '\\' . $addonCameled . 'Details';

                    if (! empty($addon) && class_exists($details_class_name)) {
                        \App::get('hooks')->callListeners('pre-payment-cc', $client->id, $cc, $total_due, $currency->code);

                        $addonCC = '\\Addon\\' . $addonCameled . '\Libraries\\' . $addonCameled . 'Cc';
                        if (class_exists($addonCC)) {
                            $payment_results = \App::factory($addonCC)->attemptCcPayment($cc->id, $currency->id, $total_due);

                            if ($payment_results) {
                                // transaction was successful and contains a unique transaction id.

                                $transaction = array(
                                    'client_id' => $client->id,
                                    'gateway_id' => $gateway->id,
                                    'invoice_id' => $invoice->id,
                                    'currency_id' => $currency->id,
                                    'description' => \App::get('translation')->get('cc_payment_via_gateway').' '.$gateway->name.' - '.$payment_results,
                                    'type' => 'invoice',
                                    'amount' => $total_due
                                );

                                $result = \App\Libraries\Payments::createTransaction($transaction);

                                if ($result) {
                                    \App::get('session')->setFlash('success', \App::get('translation')->get('payment_successfully_taken'));
                                    $this->updateInvoice($invoice->id);
                                    return true;

                                } else {
                                    \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                                }
                            } else {
                                \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                            }
                        }
                    }
                }
            }

        } elseif ($method_type == 'stored_ach') {
            $ach_explode = explode('ach_', $data['account']);
            $ach_id = $ach_explode[1];

            $ach = \ClientAch::find($ach_id);

            if (
                $invoice->client_id === $ach->client_id &&
                $ach->is_active == '1' &&
                $invoice->currency_id === $ach->currency_id
            ) {
                // The account is valid and belongs to this client.

                $gateway = $ach->Gateway()->first();
                $gateway_currency = \GatewayCurrency::where('gateway_id', '=', $gateway->id)->where('currency_id', '=', $currency->id)->first();

                if (! empty($gateway) && $gateway->is_merchant == '1' && ! empty($gateway_currency)) {
                    // We found the gateway, and it's a merchant so we're good to go!
                    // Load up the merchant gateway details to see if it accepts storage
                    // of ach accounts.
                    $addon = $gateway->addon()->first();
                    $addonCameled = Str::studly($addon->directory);

                    $details_class_name = '\\Addon\\' . $addonCameled . '\\' . $addonCameled . 'Details';

                    if (! empty($addon) && class_exists($details_class_name)) {
                        \App::get('hooks')->callListeners('pre-payment-ach', $client->id, $ach, $total_due, $currency->code);

                        $addonACH = '\\Addon\\' . $addonCameled . '\Libraries\\' . $addonCameled . 'Ach';
                        if (class_exists($addonACH)) {
                            $payment_results = \App::factory($addonACH)
                                ->attemptAchPayment($ach->id, $currency->id, $total_due);

                            if ($payment_results) {
                                // transaction was successful and contains a unique transaction id.
                                $transaction = array(
                                    'client_id' => $client->id,
                                    'gateway_id' => $gateway->id,
                                    'invoice_id' => $invoice->id,
                                    'currency_id' => $currency->id,
                                    'description' => \App::get('translation')->get('ach_payment_via_gateway').' '.$gateway->name.' - '.$payment_results,
                                    'type' => 'invoice',
                                    'amount' => $total_due
                                );

                                $result = \App\Libraries\Payments::createTransaction($transaction);

                                if ($result) {
                                    \App::get('session')->setFlash('success', \App::get('translation')->get('payment_successfully_taken'));
                                    $this->updateInvoice($invoice->id);
                                    return true;

                                } else {
                                    \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                                }
                            } else {
                                \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                            }
                        }
                    }
                }
            }

        } elseif ($method_type == 'cc') {
            // Non-stored CC
            $gateway_link = \GatewayCurrency::where('currency_id', '=', $currency->id)->get();

            foreach ($gateway_link as $link) {
                $gateway = $link->Gateway()
                    ->where('is_merchant', '=', 1)
                    ->where('process_cc', '=', 1)
                    ->where('is_active', '=', 1)
                    ->orderBy('sort', 'asc')
                    ->first();

                if (is_object($gateway) && ! empty($gateway)) {
                    break;
                }
            }

            if (! empty($gateway)) {
                // We found the gateway, and it's a merchant so we're good to go!
                $addon = $gateway->addon()->first();
                $addonCameled = Str::studly($addon->directory);

                $details_class_name = '\\Addon\\' . $addonCameled . '\\' . $addonCameled . 'Details';

                if (! empty($addon) && class_exists($details_class_name)) {
                    \App::get('hooks')->callListeners('pre-payment-manual-cc', $client->id, $total_due, $currency->code);

                    $addonCC = '\\Addon\\' . $addonCameled . '\Libraries\\' . $addonCameled . 'Cc';
                    if (class_exists($addonCC)) {
                        $payment_results = \App::factory($addonCC)
                            ->attemptCcPayment(0, $currency->id, $total_due, $data, $client->id);

                        if ($payment_results) {
                            // transaction was successful and contains a unique transaction id.
                            $transaction = array(
                                'client_id' => $client->id,
                                'gateway_id' => $gateway->id,
                                'invoice_id' => $invoice->id,
                                'currency_id' => $currency->id,
                                'description' => \App::get('translation')->get('cc_payment_via_gateway').' '.$gateway->name.' - '.$payment_results,
                                'type' => 'invoice',
                                'amount' => $total_due
                            );

                            $result = \App\Libraries\Payments::createTransaction($transaction);

                            if ($result) {
                                \App::get('session')->setFlash('success', \App::get('translation')->get('payment_successfully_taken'));
                                $this->updateInvoice($invoice->id);
                                return true;

                            } else {
                                \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                            }
                        } else {
                            \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                        }
                    }
                }
            }

        } elseif ($method_type == 'ach') {
            $gateway_link = \GatewayCurrency::where('currency_id', '=', $currency->id)->get();

            foreach ($gateway_link as $link) {
                $gateway = $link->Gateway()
                    ->where('is_merchant', '=', 1)
                    ->where('process_ach', '=', 1)
                    ->where('is_active', '=', 1)
                    ->orderBy('sort', 'asc')
                    ->first();

                if (is_object($gateway) && ! empty($gateway)) {
                    break;
                }
            }

            if (! empty($gateway)) {
                // We found the gateway, and it's a merchant so we're good to go!
                $addon = $gateway->addon()->first();
                $addonCameled = Str::studly($addon->directory);

                $details_class_name = '\\Addon\\' . $addonCameled . '\\' . $addonCameled . 'Details';

                if (! empty($addon) && class_exists($details_class_name)) {
                    \App::get('hooks')->callListeners('pre-payment-manual-ach', $client->id, $total_due, $currency->code);

                    $addonACH = '\\Addon\\' . $addonCameled . '\Libraries\\' . $addonCameled . 'Ach';
                    if (class_exists($addonACH)) {
                        $payment_results = \App::factory($addonACH)
                            ->attemptAchPayment(0, $currency->id, $total_due, $data, $client->id);

                        if ($payment_results) {
                            // transaction was successful and contains a unique transaction id.

                            $transaction = array(
                                'client_id' => $client->id,
                                'gateway_id' => $gateway->id,
                                'invoice_id' => $invoice->id,
                                'currency_id' => $currency->id,
                                'description' => \App::get('translation')->get('ach_payment_via_gateway').' '.$gateway->name.' - '.$payment_results,
                                'type' => 'invoice',
                                'amount' => $total_due
                            );

                            $result = \App\Libraries\Payments::createTransaction($transaction);

                            if ($result) {
                                \App::get('session')->setFlash('success', \App::get('translation')->get('payment_successfully_taken'));
                                $this->updateInvoice($invoice->id);
                                return true;

                            } else {
                                \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                            }
                        } else {
                            \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                        }
                    }
                }
            }

            // Non-stored ACH
        } elseif (strpos($method_type, 'gateway_') !== false) {
            // Gateway
            $gateway_explode = explode('gateway_', $method_type);
            $gateway_slug = $gateway_explode[1];

            $gateway = \Gateway::where('slug', '=', $gateway_slug)->first();
            $addon = $gateway->Addon()->first();

            if (! empty($gateway)) {
                $gateway_currency = \GatewayCurrency::where('gateway_id', '=', $gateway->id)->where('currency_id', '=', $currency->id)->first();

                if (!empty($gateway_currency)) {
                    // gateway exists and supports the currency.

                    \App::get('hooks')->callListeners('pre-payment-manual-gateway', $client->id, $total_due, $currency->code);

                    $gateway_data = array(
                        'invoice_id' => $invoice->id,
                        'client_id' => $client->id,
                        'currency_id' => $currency->id,
                        'total_due' => $total_due,
                        'data' => $data
                    );

                    $result = \App\Libraries\Payments::takePayment($addon, $gateway_data);

                    if ($result === true) {
                        // check if it's an offline payment

                        if ($addon->directory == 'offlinepayment' && \App::checkInstalledAddon('offlinepayment')) {
                            return \App::redirect('client-offlinepayment-finalpage');
                        } else {
                            \App::get('session')->setFlash('success', \App::get('translation')->get('payment_successfully_taken'));
                            return true;
                        }
                    } else {
                        \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                    }
                }
            }

        } elseif ($method_type == 'credit') {
            // Pay using account credit.

            // double check we still have enough account credit
            $accountCredit = \App\Libraries\Transactions::clientCredit(
                $invoice->client_id,
                $invoice->currency_id,
                true,
                false
            );

            if ($accountCredit >= $total_due) {
                $transaction = array(
                    'client_id' => $client->id,
                    'gateway_id' => 0,
                    'invoice_id' => $invoice->id,
                    'currency_id' => $currency->id,
                    'description' => \App::get('translation')->get('invoice_payment_from_credit_balance'),
                    'type' => 'credit_usage',
                    'amount' => $total_due
                );

                $result = \App\Libraries\Payments::createTransaction($transaction);

                if ($result) {
                    \App::get('session')->setFlash('success', \App::get('translation')->get('payment_successfully_taken'));
                    $this->updateInvoice($invoice->id);
                    return true;
                } else {
                    \App::get('session')->setFlash('error', \App::get('translation')->get('error_taking_payment'));
                }
            } else {
                \App::get('session')->setFlash('error', \App::get('translation')->get('insufficient_funds'));
            }
        }

        // If all else fails, we'll return a false response to confirm something
        // didn't go to plan.
        return false;
    }
}
