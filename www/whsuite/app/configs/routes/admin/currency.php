<?php

$routes = array(

    /*
     * Currency management routes
     */
    'currency' => array(
        'path' => '/currencies/',
        'values' => array(
            'controller' => 'CurrenciesController',
            'action' => 'index'
        )
    ),
    'currency-add' => array(
        'path' => '/currencies/add/',
        'values' => array(
            'controller' => 'CurrenciesController',
            'action' => 'form'
        )
    ),
    'currency-edit' => array(
        'path' => '/currencies/edit/{:id}/',
        'values' => array(
            'controller' => 'CurrenciesController',
            'action' => 'form'
        )
    ),
    'currency-delete' => array(
        'path' => '/currencies/delete/{:id}/',
        'values' => array(
            'controller' => 'CurrenciesController',
            'action' => 'delete'
        )
    )
);