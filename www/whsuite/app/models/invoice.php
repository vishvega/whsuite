<?php

class Invoice extends AppModel
{

    public static $status_types = array(
        '0' => 'unpaid',
        '1' => 'paid',
        '2' => 'void'
    );

    public function InvoiceItem()
    {
        return $this->hasMany('InvoiceItem');
    }

    public function Currency()
    {
        return $this->belongsTo('Currency');
    }

    public function Client()
    {
        return $this->belongsTo('Client');
    }

    public function Transaction()
    {
        return $this->hasMany('Transaction');
    }

    public function Order()
    {
        return $this->hasOne('Order');
    }

    /**
     * Delete
     *
     * Override the default delete method to remove invoice items
     *
     * @return bool true if the delete was successful
     */
    public function delete()
    {
        // delete all invoice items
        $InvoiceItems = \InvoiceItem::where('invoice_id', '=', $this->id)
            ->delete();

        return parent::delete();
    }

    /**
     * format the status list and translate ready for drop downs
     *
     * @return      array
     */
    public static function formattedStatusList()
    {
        $statuses = array();
        foreach (self::$status_types as $id => $status) {
            $statuses[$id] = App::get('translation')->get($status);
        }

        return $statuses;
    }

    /**
     * create a main invoice row
     *
     * @param   object      $client     Client object to attach the invoice too
     * @param   array       $tax        Array containing the two tax lebels to apply
     * @param   array       $options    Any options to pass into the createInvoice method
     * @return  int|bool    $invoiceId  Invoice ID on success, false on fail
     */
    public static function createInvoice($client, $tax, $options = array())
    {
        $invoice_no = App::get('configs')->get('settings.billing.next_invoice_number');

        Setting::where('slug', '=', 'next_invoice_number')->increment('value');

        $Carbon = \Carbon\Carbon::now(
            \App::get('configs')->get('settings.localization.timezone')
        );

        if (! isset($options['futureDueDate']) || $options['futureDueDate'] === true) {
            $Carbon->addDays(
                \App::get('configs')->get('settings.billing.invoice_days')
            );
        }

        if (isset($options['Currency']) && is_object($options['Currency'])) {
            $currencyId = $options['Currency']->id;
        } else {
            $currencyId = $client->currency_id;
        }

        $invoice = new Invoice;
        $invoice->invoice_no = $invoice_no;
        $invoice->client_id = $client->id;
        $invoice->date_due = $Carbon->toDateTimeString(); // To begin with we just set this to 30 days in advance
        $invoice->currency_id = $currencyId;
        $invoice->subtotal = '0';
        $invoice->level1_rate = $tax['level1'];
        $invoice->level2_rate = $tax['level2'];
        $invoice->total = '0';
        $invoice->status = '0';

        if ($invoice->save()) {
            return $invoice->id;
        } else {
            return false;
        }
    }

    /**
     * count the unpaid invoices for the shortcut label
     *
     * @return  int       number of unpaid invoices
     */
    public static function countUnpaid()
    {
        $now = \Carbon\Carbon::now();
        $date = $now->toDateString();

        $instance = new static;
        $query = $instance->newQuery();

        return $query->where('status', '=', 0)
            ->where('date_due', '>=', $date)
            ->count();
    }

    /**
     * count the overdue invoices for the shortcut label
     *
     * @return  int       number of overdue invoices
     */
    public static function countOverdue()
    {
        $now = \Carbon\Carbon::now();
        $date = $now->toDateString();

        $instance = new static;
        $query = $instance->newQuery();

        return $query->where('status', '=', 0)
            ->where('date_due', '<', $date)
            ->count();
    }
}
