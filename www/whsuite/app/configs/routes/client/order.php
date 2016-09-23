<?php

$routes = array(
    /**
     * Order
     */
    'order' => array(
        'path' => '/order/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'listing'
        )
    ),
    'order-switch' => array(
        'params' => array(
            'category_id' => '(\d+)',
            'currency_id' => '(\d+)',
        ),
        'path' => '/order/group/{:group_id}/currency/{:currency_id}/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'listing'
        )
    ),
    'order-new-item' => array(
        'params' => array(
            'product_id' => '(\d+)',
        ),
        'path' => '/order/product/{:product_id}/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'configureItem'
        )
    ),
    'order-edit-item' => array(
        'params' => array(
            'product_id' => '(\d+)',
            'item_id' => '(\d+)',
        ),
        'path' => '/order/product/{:product_id}/{:item_id}/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'configureItem'
        )
    ),
    'order-delete-item' => array(
        'params' => array(
            'product_id' => '(\d+)',
            'item_id' => '(\d+)',
        ),
        'path' => '/order/product/{:product_id}/{:item_id}/delete/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'deleteItem'
        )
    ),

    'view-cart' => array(
        'path' => '/cart/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'viewCart'
        )
    ),

    'domain-lookup-response' => array(
        'params' => array(
            'currency_id' => '(\d+)',
        ),
        'path' => '/order/domain-lookup-response/{:currency_id}/',
        'values' => array(
            'controller' => 'OrderController',
            'action' => 'domainLookupResponse'
        )
    ),
);
