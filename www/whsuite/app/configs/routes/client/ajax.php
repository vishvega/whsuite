<?php

$routes = array(

    'ajax-pay-button-check' => array(
        'path' => '/ajax/gateways/has-pay-button/{:gateway}/{:invoice_id}/',
        'params' => array(
            'search' => '([a-zA-Z0-9\.\+-_]+)',
            'invoice_id' => '(\d+)'
        ),
        'values' => array(
            'controller' => 'GatewaysController',
            'action' => 'hasPayButton'
        )
    ),

    'ajax-pay-button-default' => array(
        'path' => '/ajax/gateways/default-pay-button/',
        'values' => array(
            'controller' => 'GatewaysController',
            'action' => 'defaultPayButton'
        )
    )

);