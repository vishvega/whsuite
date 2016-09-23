<?php

$routes = array(
    /**
     * Credit/Debit Cards
     */
    'manage-cc' => array(
        'path' => '/billing/manage-cc/{:id}/',
        'values' => array(
            'controller' => 'CcController',
            'action' => 'manageCc'
        )
    ),
    'add-cc' => array(
        'path' => '/billing/add-cc/',
        'values' => array(
            'controller' => 'CcController',
            'action' => 'addCc'
        )
    ),
    'delete-cc' => array(
        'path' => '/billing/delete-cc/{:id}/',
        'values' => array(
            'controller' => 'CcController',
            'action' => 'deleteCc'
        )
    )
);
