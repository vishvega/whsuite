<?php

$routes = array(
    /**
     * Invoices
     */
    'invoices' => array(
        'path' => '/invoices/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'index'
        )
    ),
    'invoices-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/invoices/{:page}/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'index'
        )
    ),
    'manage-invoice' => array(
        'path' => '/invoice/{:id}/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'manageInvoice'
        )
    ),
    'invoice-download' => array(
        'path' => '/invoice/{:id}/download/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'downloadInvoice'
        )
    ),
    'invoice-pay' => array(
        'path' => '/invoice/{:id}/pay/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'payInvoice'
        )
    ),
);
