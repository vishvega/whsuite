<?php

namespace App\Libraries;

class Transactions
{
    public static function clientCredit($id, $currency_id, $hide_symbols = false, $format = true)
    {
        if (is_null($client = \Client::find($id)) || is_null($currency = \Currency::find($currency_id))) {
            return 0;
        }

        $transactions = \Transaction::where('client_id', '=', $id)->where('currency_id', '=', $currency_id)->get();
        $client_credit = 0;

        $debitTypes = array('credit_usage', 'debit');

        foreach ($transactions as $transaction) {
            $isCreditInvoice = $transaction->type == 'invoice' && $transaction->invoice_id == '0';

            if ($transaction->type == 'receipt' || $isCreditInvoice) {
                $client_credit = $client_credit + $transaction->amount;
            } elseif (in_array($transaction->type, $debitTypes)) {
                $client_credit = $client_credit - $transaction->amount;
            } else {
                // void, refunded
                continue;
            }
        }

        if ($format) {
            return \App::get('money')->format($client_credit, $currency->code, $hide_symbols);
        } else {
            return $client_credit;
        }
    }

    public static function allClientCredits($id)
    {
        if (is_null($client = \Client::find($id))) {
            return 0;
        }

        $client_totals = array();

        foreach (\Currency::all() as $currency) {
            $client_totals[$currency->code] = self::clientCredit($id, $currency->id, true);
        }

        return $client_totals;
    }


    /**
     * format transactions status for output in the template list
     *
     * @param   string  type string
     * @param   bool    (optional) Output with html badge
     * @return  string  HTML string to output
     */
    public static function formatTransactionType($type, $html = true)
    {
        $lang = \App::get('translation');

        if (in_array($type, array('invoice', 'credit_usage'))) {
            $class = 'success';
            $text = $lang->get('payment');
        } elseif ($type === 'receipt') {
            $class = 'warning';
            $text = $lang->get('credit_issued');
        } else {
            $class = 'default';
            $text = $lang->get($type);
        }

        if ($html) {
            return sprintf('<span class="label label-%s">%s</span>', $class, $text);
        } else {
            return $text;
        }
    }

    /**
     * invoice paid - add account credit
     *
     * @param   object          $Invoice            The invoice that has just been paid
     * @return  bool|string     $result|$licenseKey Bool false on fail or the license key if success
     */
    public static function addAccountCredit($Invoice)
    {
        $amount = 0;

        $InvoiceItems = $Invoice->InvoiceItem()->get();
        foreach ($InvoiceItems as $Item) {
            if ((bool)$Item->is_account_credit === true) {
                $amount += $Item->total;
            }
        }

        if ($amount > 0) {
            return \App\Libraries\Payments::createTransaction(
                array(
                    'client_id' => $Invoice->client_id,
                    'gateway_id' => null,
                    'currency_id' => $Invoice->currency_id,
                    'description' => \App::get('translation')->get('credit_issued'),
                    'type' => 'receipt',
                    'amount' => $amount,
                    'invoice_id' => $Invoice->id
                )
            );
        }

        return true;
    }
}
