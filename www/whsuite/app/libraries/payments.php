<?php

namespace App\Libraries;

class Payments
{
    /**
     * create a new transaction record
     *
     * @param   array   array of the transaction details to save
     * @return  bool    bool result from model->save()
     */
    public static function createTransaction($data)
    {
        $transaction = new \Transaction();

        if (isset($data) && ! empty($data)) {

            foreach ($data as $var_name => $value) {

                $transaction->{$var_name} = $value;
            }
        }

        return $transaction->save();
    }

    /**
     * given the invoice id create a transaction and update the invoice
     *
     * @param       int     Invoice Id
     * @param       mixed   Token from the gateway
     * @param       object  Object for the gateway addon
     * @return
     */
    public static function finalisePayment($invoice_id, $reference, $addon)
    {
        $invoice = \Invoice::find($invoice_id);

        $transaction = array(
            'client_id' => $invoice->client_id,
            'gateway_id' => $addon->Gateway->id,
            'invoice_id' => $invoice->id,
            'currency_id' => $invoice->currency_id,
            'description' => \App::get('translation')->get('payment_via_gateway').' '.$addon->Gateway->name.' - '.$reference,
            'gateway_token' => $reference,
            'type' => 'invoice',
            'amount' => ($invoice->total - $invoice->total_paid)
        );

        $return = self::createTransaction($transaction);

        if ($return) {

            \App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($invoice_id);
        }

        return $return;
    }


    /**
     * given the gateway and the order information, try to take pay
     *
     * @param   object      DB Object for the gateway we are using (addon)
     * @param   array       Array of data for this transaction
     * @return  bool
     */
    public static function takePayment($addon, $data)
    {
        // try to load the gateway
        $gateway = \App::factory('Addon\\'.ucfirst($addon->directory).'\Libraries\\'.$addon->directory);

        // check we've got a proper gateway
        if (! is_object($gateway)) {

            return false;
        }

        // allow the gateway to prevent transaction, return false from the setup
        if (! $gateway->setup($data)) {

            return false;
        }

        return $gateway->process($data);
    }

    /**
     * check the return of the transaction
     *
     * @param   object      DB Object for the gateway we are using (addon)
     * @param   array       Array of data for this transaction
     * @return  bool
     */
    public static function checkPayment($addon, $data)
    {
        // try to load the gateway
        $gateway = \App::factory('Addon\\'.ucfirst($addon->directory).'\Libraries\\'.$addon->directory);

        // check we've got a proper gateway
        if (! is_object($gateway)) {

            return false;
        }

        // allow the gateway to prevent transaction, return false from the setup
        if (! $gateway->setup($data, true)) {

            return false;
        }

        return $gateway->checkReturn($data);
    }

    /**
     * check the transaction reference so we can double check
     * if the transaction has already been logged or not
     *
     * @param   int     Invoice ID the transaction should belong to
     * @param   mixed   The reference from the gateway
     * @return  bool    true - found / false - not found
     */
    public static function referenceExists($invoice_id, $reference)
    {
        $transaction = \Transaction::where('invoice_id', '=', $invoice_id)
            ->where('gateway_token', '=', $reference)
            ->first();

        if (is_object($transaction)) {

            return true;
        } else {

            return false;
        }
    }

}
