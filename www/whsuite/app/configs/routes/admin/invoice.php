<?php

$routes = array(
   // INVOICES
    'client-invoices' => array(
        'path' => '/client/profile/{:id}/invoices/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'listClientInvoices'
        )
    ),
    'client-invoices-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/invoices/{:page}/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'listClientInvoices'
        )
    ),

    'invoice' => array(
        'path' => '/invoices/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'listInvoices'
        )
    ),
    'invoice-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/invoices/{:page}/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'listInvoices'
        )
    ),
    'client-invoice' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/invoice/{:invoice_id}/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'viewInvoice'
        )
    ),
    'client-invoice-update' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/invoice/{:invoice_id}/update/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'updateInvoice'
        )
    ),

    'client-invoice-update-settings' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/invoice/{:invoice_id}/update-settings/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'updateInvoiceSettings'
        )
    ),
    'client-invoice-apply-credit' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/invoice/{:invoice_id}/apply-credit/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'applyCreditToInvoice'
        )
    ),
    'client-invoice-add-payment' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/invoice/{:invoice_id}/add-payment/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'addPaymentToInvoice'
        )
    ),
    'invoice-download' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/invoices/download/{:invoice_id}/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'downloadInvoice'
        )
    ),
    'invoice-email' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/invoices/email/{:invoice_id}/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'emailInvoice'
        )
    ),
    'invoice-transaction-delete' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/invoices/{:invoice_id}/delete-transaction/{:id}/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'deleteTransaction'
        )
    ),
    'invoice-capture-payment' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/invoices/{:invoice_id}/capture/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'captureInvoice'
        )
    ),
    'invoice-void' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/invoices/{:invoice_id}/void/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'voidInvoice'
        )
    ),
    'invoice-unvoid' => array(
        'params' => array(
            'invoice_id' => '(\d+)'
        ),
        'path' => '/invoices/{:invoice_id}/unvoid/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'unvoidInvoice'
        )
    )
);
