<?php

$routes = array(

    /*
     * Gateway management routes
     */
    'gateway' => array(
        'path' => '/gateways/',
        'values' => array(
            'controller' => 'GatewaysController',
            'action' => 'index'
        )
    ),
    'gateway-edit' => array(
        'path' => '/gateways/edit/{:id}/',
        'values' => array(
            'controller' => 'GatewaysController',
            'action' => 'form'
        )
    )
);