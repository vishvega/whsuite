<?php
/**
 * Invoices Controller
 *
 * The invoice controller handles all invoice related admin methods.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class InvoiceController extends AdminController
{
    /**
     * List Invoices
     *
     * @param integer $page Current page number
     * @param integer $id ID of the client
     * @return View
     */
    public function listInvoices($page = 1, $id = null)
    {
        if ($id) {
            $client = Client::find($id);
            if (empty($client)) {
                return $this->redirect('admin-client');
            }

            // We're only listing the client's invoices.
            $conditions = array(
                array(
                    'type' => 'where',
                    'column' => 'client_id',
                    'operator' => '=',
                    'value' => $id
                )
            );

            $title = $this->lang->get('invoices');

            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
            App::get('breadcrumbs')->add(
                $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
                'admin-client-profile',
                array('id' => $client->id)
            );
            App::get('breadcrumbs')->add($title);

            $this->view->set('client', $client);

            $invoices = Invoice::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'id', 'desc', 'admin-client-invoices-paging', array('id' => $id, 'page' => $page));
        } else {
            // We're listing everyones invoices
            $invoices = Invoice::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, null, 'id', 'desc', 'admin-invoice-paging', array('page' => $page));

            $title = $this->lang->get('invoices');
            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($title);
        }


        App::get('breadcrumbs')->build();
        $this->view->set('title', $title);
        $this->view->set('invoices', $invoices);
        $this->view->display('invoices/list.php');
    }

    /**
     * List Client Invoices
     *
     * @param integer $id ID of the client
     * @param integer $page Current page number
     * @return listInvoices
     */
    public function listClientInvoices($id, $page = 1)
    {
        return $this->listInvoices($page, $id);
    }

    /**
     * View Invoice
     *
     * @param integer $id ID of the client
     * @param integer $invoice_id ID of the invoice
     * @return View
     */
    public function viewInvoice($id, $invoice_id)
    {
        $client = Client::find($id);
        $invoice = Invoice::find($invoice_id);
        if (empty($client) || empty($invoice)) {
            return $this->redirect('admin-client');
        }

        if ($invoice->client_id != $id) {
            return $this->redirect('admin-client');
        }

        $items = $invoice->InvoiceItem()->get();
        $itemList = array();

        foreach ($items as $item) {
            $itemList[$item->id] = $item->toArray();
        }

        \Whsuite\Inputs\Post::set('item', $itemList);
        \Whsuite\Inputs\Post::set('invoice', $invoice->toArray());

        $title = $this->lang->get('view_invoice').' ('.$invoice->invoice_no.')';

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $gateways = Gateway::getGateways();

        $transactions = $invoice->Transaction()->with(
            array(
                'Currency',
                'Client'
            )
        )->get();

        $this->view->set('invoice', $invoice);
        $this->view->set('items', $items);
        $this->view->set('client', $client);
        $this->view->set('gateways', $gateways);
        $this->view->set('currency', $invoice->Currency()->first());
        $this->view->set('transactions', $transactions);
        $this->view->set('client_credit', \App\Libraries\Transactions::clientCredit($id, $invoice->currency_id));
        $this->view->set('title', $title);
        $this->view->display('invoices/viewInvoice.php');
    }

    /**
     * Update Invoice
     *
     * @param integer $id ID of the client
     * @param integer $invoice_id ID of the invoice
     * @return Header Redirect to invoice
     */
    public function updateInvoice($id, $invoice_id)
    {
        if (\Whsuite\Inputs\Post::get()) {
            $client = Client::find($id);
            $invoice = Invoice::find($invoice_id);
            if (empty($client) || empty($invoice)) {
                return $this->redirect('admin-client');

            }

            if ($invoice->client_id != $id) {
                return $this->redirect('admin-client');
            }

            $invoice_data = \Whsuite\Inputs\Post::get();

            // Firstly, lets delete any items we dont want anymore.
            if (isset($invoice_data['delete_item'])) {
                foreach ($invoice_data['delete_item'] as $id => $value) {
                    if ($value == '1') {
                        // Lets just do a few checks to make sure this item actually belongs to this invoice
                        // We wouldn't want to allow someone to go and delete another invoices item!
                        $item = InvoiceItem::find($id);
                        if ($item && $item->invoice_id == $invoice_id) {
                            // ok the item does exist, and its linked to this invoice...now lets exterminate it!
                            $item->delete();
                        }
                    }
                }
            }

            $items_post = array();
            if (isset($invoice_data['item']) && ! empty($invoice_data['item'])) {
                $items_post = $invoice_data['item'];
            }

            // Time to update any existing invoice items.
            foreach ($invoice->InvoiceItem()->get() as $item) {
                if (! isset($items_post[$item->id])) {
                    continue;
                }

                $item_post = $items_post[$item->id];

                if (isset($item_post['description'])) {
                    $item->description = $item_post['description'];
                }

                if (isset($item_post['discount'])) {
                    $item->promotion_discount = $item_post['discount'];
                }
                $item->promotion_is_percentage = $item_post['discount_percentage'];
                $item->promotion_before_tax = $item_post['discount_before_tax'];

                $item->total = $item_post['total'];
                $item->is_taxed = $item_post['is_taxed'];
                $item->save();
            }

            // Now add any new invoice items
            if (isset($invoice_data['newitem']) && $invoice_data['newitem']['description'] !='' && $invoice_data['newitem']['total'] !='') {
                $newitem = $invoice_data['newitem'];

                $item = new InvoiceItem;
                $item->invoice_id = $invoice_id;
                $item->client_id = $client->id;
                $item->description = $newitem['description'];
                $item->is_taxed = $newitem['is_taxed'];
                $item->total = $newitem['total'];
                $item->date_due = $invoice->date_due;
                $item->save();
            }

            // And finally, do an update so we get all the correct figures.
            App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($invoice_id);

            // All done! Time to redirect back to the updated invoice!
            App::get('session')->setFlash('success', $this->lang->get('invoice_updated'));
            return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice_id]);

        } else {
            return $this->redirect('admin-client');
        }
    }

    /**
     * Void Invoice
     *
     * @param integer $invoice_id ID of the invoice
     * @return Header Redirect to invoice
     */
    public function voidInvoice($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if (empty($invoice)) {
            return $this->redirect('admin-home');
        }

        $client = $invoice->Client()->first();

        $invoice->status = '2';

        if ($invoice->save()) {
            App::get('session')->setFlash('success', $this->lang->get('invoice_updated'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('invoice_update_failed'));
        }

        return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice_id]);
    }

    /**
     * Unvoid Invoice
     *
     * @param integer $invoice_id ID of the invoice
     * @return Header Redirect to invoice
     */
    public function unvoidInvoice($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if (empty($invoice)) {
            return $this->redirect('admin-home');
        }

        $client = $invoice->Client()->first();

        if ($invoice->total <= $invoice->total_paid) {
            $invoice->status = '1';
        } else {
            $invoice->status = '0';
        }

        if ($invoice->save()) {
            App::get('session')->setFlash('success', $this->lang->get('invoice_updated'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('invoice_update_failed'));
        }

        return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice_id]);
    }

    /**
     * Download Invoice
     *
     * @param integer $invoice_id ID of the invoice
     * @return void Forced download of the invoice pdf
     */
    public function downloadInvoice($invoice_id)
    {
        return App::factory('\App\Libraries\InvoiceHelper')->generateInvoicePdf($invoice_id);
    }

    /**
     * Email Invoice
     *
     * @param integer $invoice_id ID of the invoice
     * @return Header Redirect to invoice
     */
    public function emailInvoice($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if (empty($invoice)) {
            return $this->redirect('admin-home');
        }
        $client = $invoice->Client()->first();

        if (App::factory('\App\Libraries\InvoiceHelper')->emailInvoice($invoice_id)) {
            App::get('session')->setFlash('success', $this->lang->get('invoice_emailed_to_client'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_emailing_invoice_to_client'));
        }

        return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice_id]);
    }

    /**
     * Add Payment To Invoice
     *
     * @param integer $id ID of the client
     * @param integer $invoice_id ID of the invoice
     * @return Header Redirect to invoice
     */
    public function addPaymentToInvoice($id, $invoice_id)
    {
        if (\Whsuite\Inputs\Post::get()) {
            $client = Client::find($id);
            $invoice = Invoice::find($invoice_id);
            if (empty($client) || empty($invoice)) {
                return $this->redirect('admin-client');
            }

            if ($invoice->client_id != $id) {
                return $this->redirect('admin-client');
            }

            $payment_data = \Whsuite\Inputs\Post::get();

            // First we need to create a receipt transaction. This logs that someone has paid the company X amount of money
            // This first transaction doesnt get applied directly to the invoice, as its essentially just customer credit
            // as far as the system is concerned.
            $receipt = new Transaction;
            $receipt->client_id = $client->id;
            $receipt->gateway_id = $payment_data['gateway'];
            $receipt->currency_id = $invoice->currency_id;
            $receipt->description = $payment_data['description'];
            $receipt->type = 'invoice';
            $receipt->amount = preg_replace("/[^0-9.]/", '', $payment_data['amount']);
            $receipt->invoice_id = $invoice_id;

            if ($receipt->save()) {
                // Transaction saved successfuly, now we need to update the invoice, then redirect back to it.
                App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($invoice->id);
                App::get('session')->setFlash('success', $this->lang->get('invoice_updated'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
            }

            return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice_id]);
        } else {
            return $this->redirect('admin-client');
        }
    }

    /**
     * Apply Credit To invoice
     *
     * @param integer $id ID of the client
     * @param integer $invoice_id ID of the invoice
     * @return Header Redirect to invoice
     */
    public function applyCreditToInvoice($id, $invoice_id)
    {
        if (\Whsuite\Inputs\Post::get()) {
            $client = Client::find($id);
            $invoice = Invoice::find($invoice_id);
            if (empty($client) || empty($invoice)) {
                return $this->redirect('admin-client');
            }

            if ($invoice->client_id != $id) {
                return $this->redirect('admin-client');
            }

            $credit_data = \Whsuite\Inputs\Post::get();

            // First we need to check if the amount that wants to be applied to the invoice, is actually available
            // as account credit. To do this we call to the transaction helper.
            $available_credit = \App\Libraries\Transactions::clientCredit($client->id, $invoice->currency_id, true);

            if ($credit_data['amount'] > $available_credit) {
                // The client doensn't have enough credit!
                App::get('session')->setFlash('error', $this->lang->get('client_insufficient_credit'));
                return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice_id]);
            }

            // The client has enough credit to cover the expense, so lets go ahead and create a new invoice transaction record.
            $transaction_description = App::get('translation')->get('manual_credit_payment').' ('.App::get('translation')->get('created_by').': '.$this->admin_user->first_name.' '.$this->admin_user->last_name.')';

            $transaction = new Transaction;
            $transaction->client_id = $client->id;
            $transaction->invoice_id = $invoice->id;
            $transaction->currency_id = $invoice->currency_id;
            $transaction->description = $transaction_description;
            $transaction->type = 'credit_usage';
            $transaction->amount = $credit_data['amount'];

            if ($transaction->save()) {
                // Transaction saved successfuly, now we need to update the invoice, then redirect back to it.
                App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($invoice->id);
                App::get('session')->setFlash('success', $this->lang->get('invoice_updated'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
            }

            return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice_id]);
        } else {
            return $this->redirect('admin-client');
        }
    }

    /**
     * Update Invoice Settings
     *
     * @param integer $id ID of the client
     * @param integer $invoice_id ID of the invoice
     * @return @return Header Redirect to invoice
     */
    public function updateInvoiceSettings($id, $invoice_id)
    {
        if (\Whsuite\Inputs\Post::get()) {
            $client = Client::find($id);
            $invoice = Invoice::find($invoice_id);
            if (empty($client) || empty($invoice)) {
                return $this->redirect('admin-client');
            }

            if ($invoice->client_id != $id) {
                return $this->redirect('admin-client');
            }

            $invoice_data = \Whsuite\Inputs\Post::get('invoice');

            $invoice->date_due = $invoice_data['date_due'];
            $invoice->level1_rate = $invoice_data['level1_rate'];
            $invoice->level2_rate = $invoice_data['level2_rate'];
            $invoice->notes = $invoice_data['notes'];
            $invoice->status = $invoice_data['status'];

            if ($invoice->save()) {
                // Invoice settings saved successfuly, now we need to update the invoice, then redirect back to it.
                App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($invoice->id);
                App::get('session')->setFlash('success', $this->lang->get('invoice_updated'));
            } else {
                App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
            }

            return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice_id]);
        } else {
            return $this->redirect('admin-client');
        }
    }

    /**
     * Delete Transaction
     *
     * @param integer $invoice_id ID of the invoice
     * @param  integer $transaction_id ID of the transaction
     * @return Header Redirect to invoice
     */
    public function deleteTransaction($invoice_id, $transaction_id)
    {
        $invoice = Invoice::find($invoice_id);
        $transaction = Transaction::find($transaction_id);
        if (empty($invoice) || empty($transaction)) {
            return $this->redirect('admin-client');
        }

        if ($invoice_id != $transaction->invoice_id) {
            return $this->redirect('admin-client');
        }

        $client = $invoice->Client()->first();

        if ($transaction->delete()) {
            App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($invoice_id);
            App::get('session')->setFlash('success', $this->lang->get('transaction_deleted'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_deleting_transaction'));
        }

        return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice->id]);
    }

    /**
     * Create Invoice
     *
     * Creates a new blank invoice for a client.
     * @param integer $id Client ID
     * @return Header Redirect to invoice
     */
    public function createInvoice($id)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        $tax = \TaxLevel::getRates($client->state, $client->country);

        $invoice_id = Invoice::createInvoice($client, $tax);

        if ($invoice_id) {
            return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice_id]);
        }

        App::get('session')->setFlash('error', $this->lang->get('error_creating_invoice'));
        return $this->redirect('admin-client-profile', ['id' => $client->id]);
    }

    /**
     * Capture Invoice
     *
     * Attempts to take payment using a client's stored credit/debit card or ach
     * account details.
     *
     * @param integer $invoice_id ID of the invoice
     * @return Header Redirect to invoice
     */
    public function captureInvoice($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if (empty($invoice)) {
            return $this->redirect('admin-client');
        }

        $client = $invoice->Client()->first();

        // Attempt the payment via the invoice helper method. This will automatically
        // update the invoice once it's done.
        App::factory('\App\Libraries\InvoiceHelper')->attemptPayment($invoice->id);

        // Once the above attemptPayment method has finished, we need to see if
        // the invoice payment status has changed. If it's paid we want to let
        // the user know it was successful. So we destroy the old $invoice variable
        // and create a new one, which will contain the new data.

        unset($invoice); // Destroy the old var as we dont need it now
        $invoice = Invoice::find($invoice_id);
        if ($invoice->status == '1') {
            // The invoice was successfully paid
            App::get('session')->setFlash('success', $this->lang->get('payment_successfully_captured'));
        } else {
            // Unable to capture payment.
            App::get('session')->setFlash('error', $this->lang->get('unable_to_capture_payment'));
        }

        return $this->redirect('admin-client-invoice', ['id' => $client->id, 'invoice_id' => $invoice->id]);
    }
}
