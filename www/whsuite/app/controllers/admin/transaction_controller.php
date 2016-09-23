<?php
/**
 * Transaction Controller
 *
 * The transaction controller handles all transaction related admin methods.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class TransactionController extends AdminController
{
    public function listTransactions($page = 1, $id = null)
    {
        if ($id) {
            $client = Client::find($id);
            if (empty($client)) {
                return $this->redirect('admin-client');
            }

            // We're only listing the client's transactions.
            $conditions = array(
                array(
                    'type' => 'where',
                    'column' => 'client_id',
                    'operator' => '=',
                    'value' => $id
                )
            );

            $title = $this->lang->get('transactions');

            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
            App::get('breadcrumbs')->add(
                $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
                'admin-client-profile',
                array('id' => $client->id)
            );
            App::get('breadcrumbs')->add($title);

            $this->view->set('client', $client);
            $this->view->set('client_credit', \App\Libraries\Transactions::allClientCredits($client->id));

            $transactions = Transaction::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'created_at', 'desc', 'admin-client-transactions-paging', array('id' => $id));
        } else {
            // We're listing everyones transactions
            $transactions = Transaction::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, null, 'created_at', 'desc', 'admin-transactions-paging', array('id' => $id));

            $title = $this->lang->get('transactions');
            App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
            App::get('breadcrumbs')->add($title);
        }

        App::get('breadcrumbs')->build();
        $this->view->set('title', $title);
        $this->view->set('transactions', $transactions);
        $this->view->display('transactions/list.php');
    }

    public function listClientTransactions($id, $page = 1)
    {
        return $this->listTransactions($page, $id);
    }

    public function newTransaction($id)
    {
        $client = Client::find($id);
        if (empty($client)) {
            return $this->redirect('admin-client');
        }

        $title = $this->lang->get('new_transaction');
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();
        $this->view->set('title', $title);

        if (\Whsuite\Inputs\Post::get()) {
            $currency_id = \Whsuite\Inputs\Post::get('currency_id');
            $gateway_id = \Whsuite\Inputs\Post::get('gateway_id');
            $type = \Whsuite\Inputs\Post::get('type');
            $amount = \Whsuite\Inputs\Post::get('amount');
            $description = \Whsuite\Inputs\Post::get('description');

            // If this type is a deduction on the clients credit balance, first chec the client
            // has sufficient credit in place to actually make the deduction.
            if ($type == 'debit') {
                $credit_balance = \App\Libraries\Transactions::clientCredit($id, $currency_id, true);

                if ($amount > $credit_balance) {
                    App::get('session')->setFlash('error', $this->lang->get('client_insufficient_credit_transaction'));
                    return $this->redirect('admin-client-new-transaction', ['id' => $client->id]);
                }
            }

            $transaction = new Transaction;
            $transaction->client_id = $client->id;
            $transaction->gateway_id = $gateway_id;
            $transaction->currency_id = $currency_id;
            $transaction->description = $description;
            $transaction->type = $type;
            $transaction->amount = $amount;


            if ($transaction->save()) {
                App::get('session')->setFlash('success', $this->lang->get('transaction_added'));
                return $this->redirect('admin-client-profile', ['id' => $client->id]);
            } else {
                App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
                return $this->redirect('admin-client-new-transaction', ['id' => $client->id]);
            }

        } else {
            $this->view->set('currencies', Currency::formattedList('id', 'code'));
            $this->view->set('gateways', Gateway::getGateways(true, true));
            $this->view->set('client', $client);
            $this->view->set('client_credit', \App\Libraries\Transactions::allClientCredits($client->id));
            $this->view->display('transactions/newTransaction.php');
        }
    }

    public function manageTransaction($id, $transaction_id)
    {
        $transaction = Transaction::find($transaction_id);
        if (! $transaction) {
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        }

        $client = $transaction->Client()->first();

        if (! isset($client->id) || $client->id != $id) {
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        }

        $title = $this->lang->get('manage_transaction');
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('client_management'), 'admin-client');
        App::get('breadcrumbs')->add(
            $this->lang->get('manage_client').' - '.$client->first_name.' '.$client->last_name,
            'admin-client-profile',
            array('id' => $client->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();
        $this->view->set('title', $title);

        if (! $transaction) {
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        }

        if (\Whsuite\Inputs\Post::get()) {
        } else {
            $this->view->set('client', $client);
            $this->view->set('transaction', $transaction);
            $this->view->set('invoice', $transaction->Invoice()->first());
            $this->view->display('transactions/manageTransaction.php');
        }
    }

    public function voidTransaction($id, $transaction_id)
    {
        $client = Client::find($id);
        $transaction = Transaction::find($transaction_id);
        if (empty($client) || empty($transaction) || $transaction->type == 'void' || $transaction->type == 'refunded') {
            return $this->redirect('admin-client');
        }

        $update_invoice = 0;
        if ($transaction->invoice_id != 0) {
            $update_invoice = $transaction->invoice_id;
        }
        $transaction->type = 'void';

        if ($transaction->save()) {
            if ($update_invoice > 0) {
                // It looks like this transaction was linked to an invoice. We
                // need to update that invoice now though as the transaction's
                // been removed from it.
                App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($update_invoice);
            }

            App::get('session')->setFlash('success', $this->lang->get('transaction_voided'));
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        }
    }

    public function refundTransaction($id, $transaction_id)
    {
        $client = Client::find($id);
        $transaction = Transaction::find($transaction_id);
        if (empty($client) || empty($transaction) || $transaction->type == 'void' || $transaction->type == 'refunded') {
            return $this->redirect('admin-client');
        }

        $update_invoice = 0;
        if ($transaction->invoice_id != 0) {
            $update_invoice = $transaction->invoice_id;
        }
        $transaction->type = 'refunded';

        if ($transaction->save()) {
            if ($update_invoice > 0) {
                // It looks like this transaction was linked to an invoice. We
                // need to update that invoice now though as the transaction's
                // been removed from it.
                App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($update_invoice);
            }

            App::get('session')->setFlash('success', $this->lang->get('transaction_refunded'));
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        }
    }

    public function removeTransactionInvoice($id, $transaction_id)
    {
        $client = Client::find($id);
        $transaction = Transaction::find($transaction_id);
        if (empty($client) || empty($transaction) || $transaction->type == 'void' || $transaction->type == 'refunded') {
            return $this->redirect('admin-client');
        }

        $update_invoice = 0;
        if ($transaction->invoice_id != '0') {
            $update_invoice = $transaction->invoice_id;
        }
        $transaction->invoice_id = '0';
        $transaction->type = 'receipt';

        if ($transaction->save()) {
            if ($update_invoice > 0) {
                // It looks like this transaction was linked to an invoice. We
                // need to update that invoice now though as the transaction's
                // been removed from it.
                App::factory('\App\Libraries\InvoiceHelper')->updateInvoice($update_invoice);
            }

            App::get('session')->setFlash('success', $this->lang->get('transaction_updated'));
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        } else {
            App::get('session')->setFlash('error', $this->lang->get('an_error_occurred'));
            return $this->redirect('admin-client-profile', ['id' => $client->id]);
        }
    }
}
