<?php
/**
 * Client Invoice Controller
 *
 * The invoice controller handles showing invoices to clients.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class InvoiceController extends ClientController
{
    /**
     * Index
     */
    public function index($page = 1, $per_page = null)
    {

        if (!$this->logged_in) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $conditions = array(
            array(
                'type' => 'where',
                'column' => 'client_id',
                'operator' => '=',
                'value' => $this->client->id
            )
        );

        $title = $this->lang->get('invoices');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $invoices = Invoice::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'created_at', 'desc', 'client-invoices-paging');
        $this->view->set('invoices', $invoices);

        return $this->view->display('invoices/index.php');
    }

    public function manageInvoice($id)
    {
        if (!$this->logged_in) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $invoice = Invoice::find($id);

        if ($invoice->client_id != $this->client->id) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $title = $this->lang->get('invoice').' #'.$invoice->invoice_no;
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($this->lang->get('invoices'), 'client-invoices');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();


        $transactions = $invoice->Transaction()
            ->with(array(
                'Currency', 'Client'
            ))
            ->get();

        $this->view->set('invoice', $invoice);
        $this->view->set('invoice_items', $invoice->InvoiceItem()->get());
        $this->view->set('transactions', $transactions);

        return $this->view->display('invoices/manageInvoice.php');
    }

    public function downloadInvoice($id)
    {
        if (! $this->logged_in) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $invoice = Invoice::find($id);

        if ($invoice->client_id != $this->client->id) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        return App::factory('\App\Libraries\InvoiceHelper')->generateInvoicePdf($invoice->id);
    }

    public function payInvoice($id)
    {
        if (! $this->logged_in) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $invoice = Invoice::find($id);

        if ($invoice->client_id != $this->client->id) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $total_due = $invoice->total - $invoice->total_paid;

        $data = \Whsuite\Inputs\Post::get();

        if (! empty($data)) {
            if (! isset($data['account']) || $data['account'] == '') {
                return header("Location: ".App::get('router')->generate('client-home'));
            }

            if ($data['account'] == 'creditcard') {
                $method = 'cc';
            } elseif ($data['account'] == 'ach') {
                $method = 'ach';
            } elseif (strpos($data['account'], 'cc_') !== false) {
                $method = 'stored_cc';
            } elseif (strpos($data['account'], 'ach_') !== false) {
                $method = 'stored_ach';
            } else {
                $method = $data['account'];
            }

            \App::factory('\App\Libraries\InvoiceHelper')->manualPayment(
                $invoice->id,
                $method,
                \Whsuite\Inputs\Post::Get()
            );

            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $title = $this->lang->get('pay_invoice');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($this->lang->get('invoices'), 'client-invoices');
        App::get('breadcrumbs')->add(
            $this->lang->get('invoice').' #'.$invoice->invoice_no,
            'client-manage-invoice',
            array('id' => $invoice->id)
        );
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        // Get the gateways available to this currency
        $gateway_currencies = GatewayCurrency::where('currency_id', '=', $invoice->currency_id)
            ->orderBy('sort', 'asc')
            ->get();
        $gateways = array();
        $merchant_gateways = array();

        foreach ($gateway_currencies as $gateway_currency) {
            $gateway = $gateway_currency->Gateway()->first();

            if ($gateway !== false && is_object($gateway)) {
                if ($gateway->is_active == '1' && $gateway->is_merchant == '0') {
                    $gateways[] = $gateway;
                } elseif ($gateway->is_active == '1' && $gateway->is_merchant == '1') {
                    $merchant_gateways[] = $gateway;
                }
            }
        }

        // Perform a check to see if credit card and ach payments are enabled globally.
        $cc_enabled = false;
        $ach_enabled = false;

        if (App::get('configs')->get('settings.billing.enable_credit_card_payments') == '1') {
            $cc_enabled = true;
        }

        if (App::get('configs')->get('settings.billing.enable_ach_payments') == '1') {
            $ach_enabled = true;
        }

        // If the current invoice currency doesnt have a merchant assigned to it
        // for processing credit cards and/or ach accounts, it's going to be useless
        // to offer CC/ACH payment options. So we do a check to first find a CC
        // gateway, and then a ACH gateway.
        $cc_processor = false;
        $ach_processor = false;

        foreach ($merchant_gateways as $gateway) {
            if ($gateway->is_active == '1' && $gateway->is_merchant == '1') {
                if ($gateway->process_cc == '1' && $cc_enabled) {
                    $cc_processor = true;
                }

                if ($gateway->process_ach == '1' && $ach_enabled) {
                    $ach_processor = true;
                }
            }
        }
        $payment_accounts = array();
        $payment_accounts_cc_select_list = array();
        $payment_accounts_ach_select_list = array();

        if ($cc_processor) {
            $ccs = $this->client->ClientCc()->get();

            foreach ($ccs as $cc) {
                $payment_accounts[] = array(
                    'name' => $cc->first_name.' '.$cc->last_name,
                    'last4' => App::get('security')->decrypt($cc->account_last4),
                    'type' => $this->lang->get('credit_card').' ('.$this->lang->get($cc->account_type).')',
                    'status' => $cc->is_active,
                    'is_default' => $cc->is_default,
                    'id' => $cc->id,
                    'account_type' => 'cc'
                );
                if ($cc->is_active == '1' && $cc->currency_id == $invoice->currency_id) {
                    $payment_accounts_cc_select_list['cc_'.$cc->id] = '************'.App::get('security')->decrypt($cc->account_last4);
                }
            }
        }

        if ($ach_processor) {
            $achs = $this->client->ClientAch()->get();

            foreach ($achs as $ach) {
                $payment_accounts[] = array(
                    'name' => $ach->first_name.' '.$ach->last_name,
                    'last4' => App::get('security')->decrypt($ach->account_last4),
                    'type' => $this->lang->get('ach_account').' ('.$this->lang->get($ach->account_type).')',
                    'status' => $ach->is_active,
                    'is_default' => $ach->is_default,
                    'id' => $ach->id,
                    'account_type' => 'ach'
                );

                if ($ach->is_active == '1' && $ach->currency_id == $invoice->currency_id) {
                    $payment_accounts_ach_select_list['ach_'.$ach->id] = '*****'.App::get('security')->decrypt($ach->account_last4);
                }
            }
        }

        // check to see if there is any account credit
        // for the currency this invoice is in.
        $accountCredit = \App\Libraries\Transactions::clientCredit(
            $invoice->client_id,
            $invoice->currency_id,
            true,
            false
        );

        if ($accountCredit >= $total_due) {
            $this->view->set('accountCreditAvailable', true);
        }

        $this->view->set('payment_accounts', $payment_accounts);
        $this->view->set('payment_accounts_cc_select_list', $payment_accounts_cc_select_list);
        $this->view->set('payment_accounts_ach_select_list', $payment_accounts_ach_select_list);
        $this->view->set('gateways', $gateways);
        $this->view->set('currency_list', Currency::formattedList('code', 'code'));
        $this->view->set('country_list', Country::getCountries());
        $this->view->set('ach_account_types', ClientAch::accountTypes());
        $this->view->set('cc_enabled', $cc_enabled);
        $this->view->set('ach_enabled', $ach_enabled);
        $this->view->set('total_due', $total_due);

        $this->view->set('invoice', $invoice);
        $this->view->set('invoice_items', $invoice->InvoiceItem()->get());

        return $this->view->display('invoices/payInvoice.php');
    }
}
