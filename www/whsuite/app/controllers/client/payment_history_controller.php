<?php
/**
 * Client Payment History Controller
 *
 * The payment history controller simply provides a paginated view of all transactions
 * for a client to view.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class PaymentHistoryController extends ClientController
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

        $title = $this->lang->get('payment_history');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $transactions = Transaction::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'created_at', 'desc', 'client-payment-history-paging');
        $this->view->set('transactions', $transactions);

        return $this->view->display('payment_history/index.php');
    }
}
