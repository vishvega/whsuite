<?php

$routes = array(
    /**
     * account credit
     */
    'account-credit' => array(
        'path' => '/account-credit/',
        'values' => array(
            'controller' => 'AccountCreditController',
            'action' => 'addCredit'
        )
    )
);