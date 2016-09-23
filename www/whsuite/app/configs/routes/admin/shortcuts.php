<?php

$routes = array(
    /**
     * Dashboard Widgets
     */
    'shortcut-clients-new' => array(
        'path' => '/shortcuts/clients/new/',
        'values' => array(
            'controller' => 'ShortcutsController',
            'action' => 'newClients'
        )
    ),

    'shortcut-orders-new' => array(
        'path' => '/shortcuts/orders/new/',
        'values' => array(
            'controller' => 'ShortcutsController',
            'action' => 'newOrders'
        )
    ),

    'shortcut-invoices-unpaid' => array(
        'path' => '/shortcuts/invoices/unpaid/',
        'values' => array(
            'controller' => 'ShortcutsController',
            'action' => 'unpaidInvoices'
        )
    ),

    'shortcut-invoices-overdue' => array(
        'path' => '/shortcuts/invoices/overdue/',
        'values' => array(
            'controller' => 'ShortcutsController',
            'action' => 'overdueInvoices'
        )
    )
);