<?php
/**
 * Client Billing Controller
 *
 * The billing controller handles both the general management of billing details,
 * as well as credit/debit card and ach account storage for clients.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class BillingController extends ClientController
{
    /**
     * Index
     */
    public function index($page = 1, $per_page = null)
    {
        if (!$this->logged_in) {
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $title = $this->lang->get('manage_billing_details');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        // We need to load up the payment accounts, so that's all credit cards and
        // ach accounts for a client. We're then going to put those two together so
        // they can be displayed in a single table. Fun times lie ahead.

        $ccs = ClientCc::getCcs($this->client_auth->getId());
        $achs = ClientAch::getAchs($this->client_auth->getId());

        $payment_accounts = array();
        $payment_accounts_cc_select_list = array();
        $payment_accounts_ach_select_list = array();

        if (! empty($ccs)) {
            foreach ($ccs as $cc) {
                $payment_accounts[] = $cc;
                if ($cc->is_active == '1') {
                    $payment_accounts_cc_select_list['cc_'.$cc->id] = '************' . $cc->account_last4;
                }
            }
        }

        if (! empty($achs)) {
            foreach ($achs as $ach) {
                $payment_accounts[] = $ach;

                if ($ach->is_active == '1') {
                    $payment_accounts_ach_select_list['ach_'.$ach->id] = '*****' . $ach->account_last4;
                }
            }
        }

        $this->view->set('payment_accounts', $payment_accounts);
        $this->view->set('payment_accounts_cc_select_list', $payment_accounts_cc_select_list);
        $this->view->set('payment_accounts_ach_select_list', $payment_accounts_ach_select_list);
        $this->view->set('currency_list', Currency::formattedList('id', 'code'));
        $this->view->set('country_list', Country::getCountries());
        $this->view->set('ach_account_types', ClientAch::accountTypes());

        $this->view->display('billing/index.php');
    }
}
