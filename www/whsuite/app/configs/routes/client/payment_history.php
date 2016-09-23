<?php

$routes = array(
    /**
     * Payment History
     */
    'payment-history' => array(
        'path' => '/payments/',
        'values' => array(
            'controller' => 'PaymentHistoryController',
            'action' => 'index'
        )
    ),
    'payment-history-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/payments/{:page}/',
        'values' => array(
            'controller' => 'PaymentHistoryController',
            'action' => 'index'
        )
    )
);
