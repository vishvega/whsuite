<?php

$routes = array(
    // ORDERS
    'order' => array(
        'path' => '/orders/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'index'
        )
    ),
    'order-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/orders/{:page}/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'index'
        )
    ),
    'order-view' => array(
        'path' => '/orders/view/{:id}/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'viewOrder'
        )
    ),
    'order-activate' => array(
        'path' => '/orders/view/{:id}/activate/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'activateOrder'
        )
    ),
    'order-pending' => array(
        'path' => '/orders/view/{:id}/pending/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'pendingOrder'
        )
    ),
    'order-terminate' => array(
        'path' => '/orders/view/{:id}/terminate/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'terminateOrder'
        )
    ),
);
