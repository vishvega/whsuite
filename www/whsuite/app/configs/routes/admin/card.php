<?php

$routes = array(
    // CREDIT CARDS AND ACH ACCOUNTS
    'clientcc-decrypt' => array(
        'params' => array(
            'cc_id' => '(\d+)',
        ),
        'path' => '/client/profile/{:id}/cc/{:cc_id}/decrypt/',
        'values' => array(
            'controller' => 'clientsController',
            'action' => 'decryptCc'
        )
    ),
    'clientach-decrypt' => array(
        'params' => array(
            'ach_id' => '(\d+)',
        ),
        'path' => '/client/profile/{:id}/ach/{:ach_id}/decrypt/',
        'values' => array(
            'controller' => 'clientsController',
            'action' => 'decryptAch'
        )
    ),
    'clientach-decrypt-routing' => array(
        'params' => array(
            'ach_id' => '(\d+)',
        ),
        'path' => '/client/profile/{:id}/ach/{:ach_id}/decrypt-routing/',
        'values' => array(
            'controller' => 'clientsController',
            'action' => 'decryptAchRouting'
        )
    ),


    'clientcc-edit' => array(
        'params' => array(
            'cc_id' => '(\d+)',
        ),
        'path' => '/client/profile/{:id}/cc/{:cc_id}/',
        'values' => array(
            'controller' => 'clientsController',
            'action' => 'editCc'
        )
    ),
    'clientach-edit' => array(
        'params' => array(
            'ach_id' => '(\d+)',
        ),
        'path' => '/client/profile/{:id}/ach/{:ach_id}/',
        'values' => array(
            'controller' => 'clientsController',
            'action' => 'editAch'
        )
    ),

    'clientcc-add' => array(
        'path' => '/client/profile/{:id}/new-cc/',
        'values' => array(
            'controller' => 'clientsController',
            'action' => 'newCc'
        )
    ),
    'clientach-add' => array(
        'path' => '/client/profile/{:id}/new-ach/',
        'values' => array(
            'controller' => 'clientsController',
            'action' => 'newAch'
        )
    ),

    'clientcc-delete' => array(
        'params' => array(
            'cc_id' => '(\d+)',
        ),
        'path' => '/client/profile/{:id}/cc/{:cc_id}/delete/',
        'values' => array(
            'controller' => 'clientsController',
            'action' => 'deleteCc'
        )
    ),
    'clientach-delete' => array(
        'params' => array(
            'ach_id' => '(\d+)',
        ),
        'path' => '/client/profile/{:id}/ach/{:ach_id}/delete/',
        'values' => array(
            'controller' => 'clientsController',
            'action' => 'deleteAch'
        )
    )
);