<?php

$routes = array(
    // TRANSACTIONS
    'client-transactions' => array(
        'path' => '/client/profile/{:id}/transactions/',
        'values' => array(
            'controller' => 'TransactionController',
            'action' => 'listClientTransactions'
        )
    ),
    'client-transactions-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/transactions/{:page}/',
        'values' => array(
            'controller' => 'TransactionController',
            'action' => 'listClientTransactions'
        )
    ),

    'transactions' => array(
        'path' => '/transactions/',
        'values' => array(
            'controller' => 'TransactionController',
            'action' => 'listTransactions'
        )
    ),
    'transactions-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/transactions/{:page}/',
        'values' => array(
            'controller' => 'TransactionController',
            'action' => 'listTransactions'
        )
    ),

    'client-new-transaction' => array(
        'path' => '/client/profile/{:id}/new-transaction/',
        'values' => array(
            'controller' => 'TransactionController',
            'action' => 'newTransaction'
        )
    ),
    'client-manage-transaction' => array(
        'params' => array(
            'transaction_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/manage-transaction/{:transaction_id}/',
        'values' => array(
            'controller' => 'TransactionController',
            'action' => 'manageTransaction'
        )
    ),
    'client-void-transaction' => array(
        'params' => array(
            'transaction_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/void-transaction/{:transaction_id}/',
        'values' => array(
            'controller' => 'TransactionController',
            'action' => 'voidTransaction'
        )
    ),
    'client-refund-transaction' => array(
        'params' => array(
            'transaction_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/refund-transaction/{:transaction_id}/',
        'values' => array(
            'controller' => 'TransactionController',
            'action' => 'refundTransaction'
        )
    ),
    'client-remove-transaction-invoice' => array(
        'params' => array(
            'transaction_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/remove-transaction-invoice/{:transaction_id}/',
        'values' => array(
            'controller' => 'TransactionController',
            'action' => 'removeTransactionInvoice'
        )
    )
);