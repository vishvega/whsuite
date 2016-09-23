<?php

$routes = array(
    /**
     * billing period management routes
     */
    'billingperiod' => array(
        'path' => '/billing-periods/',
        'values' => array(
            'controller' => 'BillingPeriodsController',
            'action' => 'index'
        )
    ),
    'billingperiod-add' => array(
        'path' => '/billing-periods/add/',
        'values' => array(
            'controller' => 'BillingPeriodsController',
            'action' => 'form'
        )
    ),
    'billingperiod-edit' => array(
        'path' => '/billing-periods/edit/{:id}/',
        'values' => array(
            'controller' => 'BillingPeriodsController',
            'action' => 'form'
        )
    )
);