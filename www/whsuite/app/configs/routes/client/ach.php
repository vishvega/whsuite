<?php

$routes = array(
    /**
     * Automated Clearing House (ACH)
     */
    'manage-ach' => array(
        'path' => '/billing/manage-ach/{:id}/',
        'values' => array(
            'controller' => 'AchController',
            'action' => 'manageAch'
        )
    ),
    'add-ach' => array(
        'path' => '/billing/add-ach/',
        'values' => array(
            'controller' => 'AchController',
            'action' => 'addAch'
        )
    ),
    'delete-ach' => array(
        'path' => '/billing/delete-ach/{:id}/',
        'values' => array(
            'controller' => 'AchController',
            'action' => 'deleteAch'
        )
    )
);
