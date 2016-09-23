<?php

$routes = array(

    'reports' => array(
        'path' => '/reports/',
        'values' => array(
            'controller' => 'ReportsController',
            'action' => 'index'
        )
    ),
    'report-all-clients' => array(
        'path' => '/reports/all-clients/',
        'values' => array(
            'controller' => 'ReportsController',
            'action' => 'allClients'
        )
    ),
    'report-all-transactions' => array(
        'path' => '/reports/all-transactions/',
        'values' => array(
            'controller' => 'ReportsController',
            'action' => 'allTransactions'
        )
    ),
    'report-transactions' => array(
        'path' => '/reports/all-transactions/{:id}/',
        'values' => array(
            'controller' => 'ReportsController',
            'action' => 'allTransactions'
        )
    ),
    'report-all-outstanding-invoices' => array(
        'path' => '/reports/all-aging-invoices/',
        'values' => array(
            'controller' => 'ReportsController',
            'action' => 'allOutstandingInvoices'
        )
    ),
    'report-outstanding-invoices' => array(
        'path' => '/reports/all-outstanding-invoices/{:id}/',
        'values' => array(
            'controller' => 'ReportsController',
            'action' => 'allOutstandingInvoices'
        )
    ),
    'report-all-invoices' => array(
        'path' => '/reports/all-invoices/',
        'values' => array(
            'controller' => 'ReportsController',
            'action' => 'allInvoices'
        )
    ),
    'report-invoices' => array(
        'path' => '/reports/all-invoices/{:id}/',
        'values' => array(
            'controller' => 'ReportsController',
            'action' => 'allInvoices'
        )
    ),
    'report-12-month-income' => array(
        'path' => '/reports/all-12-month-income/{:id}/',
        'values' => array(
            'controller' => 'ReportsController',
            'action' => 'all12MonthIncome'
        )
    ),
);
