<?php
/**
 * Admin Reports Controller
 *
 * The reports controller provides a way to export information about your
 * company for WHSuite, that aid accounting and financial tracking.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class ReportsController extends AdminController
{
    public $helper;

    public function onLoad()
    {
        parent::onLoad();

        $this->helper = App::factory("\App\Libraries\ReportHelper");
    }

    public function index($page = 1, $per_page = null)
    {
        $title = $this->lang->get('reports');

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);
        $this->view->set('currencies', Currency::all());

        $this->view->display('reports/index.php');
    }

    public function allClients()
    {
        $clients = Client::Member()->get();

        $clients_data = $clients->toArray();

        $exclude_fields = array(
            'city',
            'status',
            'permissions'
        );

        $clients_data = $this->helper->removeArrayItems($clients_data, $exclude_fields);

        return $this->helper->generateCsv($clients_data, 'all-clients.csv');
    }

    public function allTransactions($id = 0)
    {
        if ($id > 0) {
            $currency = Currency::find($id);
            $transactions = Transaction::where('currency_id', '=', $id)->get();

            $filename = 'all-transactions-'.$currency->code.'.csv';
        } else {
            $transactions = Transaction::get();

            $filename = 'all-transactions.csv';
        }

        $transactions_data = $transactions->toArray();

        return $this->helper->generateCsv($transactions_data, $filename);
    }

    public function allOutstandingInvoices($id = 0)
    {
        if ($id > 0) {
            $currency = Currency::find($id);
            $invoices = Invoice::where('currency_id', '=', $id)->where('status', '=', '0')->whereRaw('total_paid < total')->get();

            $filename = 'outstanding-invoices-'.$currency->code.'.csv';
        } else {
            $invoices = Invoice::where('status', '=', '0')->whereRaw('total_paid < total')->get();

            $filename = 'outstanding-invoices.csv';
        }
        $invoices_data = $invoices->toArray();

        return $this->helper->generateCsv($invoices_data, $filename);
    }

    public function allInvoices($id = 0)
    {
        if ($id > 0) {
            $currency = Currency::find($id);
            $invoices = Invoice::where('currency_id', '=', $id)->get();

            $filename = 'all-invoices-'.$currency->code.'.csv';
        } else {
            $invoices = Invoice::get();

            $filename = 'all-invoices.csv';
        }
        $invoices_data = $invoices->toArray();

        return $this->helper->generateCsv($invoices_data, $filename);
    }

    public function all12MonthIncome($id)
    {
        $CarbonStart = \Carbon\Carbon::now(
            \App::get('configs')->get('settings.localization.timezone')
        );
        $CarbonEnd = $CarbonStart->copy();
        $CarbonStart->subYear();

        $start = $CarbonStart->toDateTimeString();
        $end = $CarbonEnd->toDateTimeString();

        $transactions = Transaction::where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->where('currency_id', '=', $id)
            ->get();

        $data = array();

        // Create our mothly array
        while ($CarbonStart->lte($CarbonEnd)) {
            $period = $CarbonStart->format('Y-m');
            $data[$period] = array(
                'period' => $period,
                'currency_id' => $id,
                'income' => 0
            );

            $CarbonStart->addMonth();
        }

        $total_income = 0;
        foreach ($transactions as $transaction) {
            $Carbon = \Carbon\Carbon::parse(
                $transaction->created_at,
                \App::get('configs')->get('settings.localization.timezone')
            );

            $data[$Carbon->format('Y-m')]['income'] += $transaction->amount;

            $total_income += $transaction->amount;
        }

        $data['total'] =  array(
            'period' => $this->lang->get('total'),
            'currency_id' => '',
            'income' => $total_income
        );

        return $this->helper->generateCsv($data, '12-month-income-'.$id.'.csv');
    }
}
