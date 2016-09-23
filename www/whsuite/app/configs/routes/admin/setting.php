<?php

$routes = array(

    // SETTINGS RELATED ROUTES
    'action-logs' => array(
        'path' => '/action-log/',
        'values' => array(
            'controller' => 'logController',
            'action' => 'viewLogs'
        )
    ),
    'action-logs-paging' => array(
        'params' => array(
            'page_id' => '(\d+)',
        ),
        'path' => '/action-log/{:page}/',
        'values' => array(
            'controller' => 'logController',
            'action' => 'viewLogs'
        )
    ),

    // System Settings
    'settings' => array(
        'path' => '/settings/',
        'values' => array(
            'controller' => 'settingsController',
            'action' => 'viewCategory'
        )
    ),
    'settings-category' => array(
        'path' => '/settings/{:id}/',
        'values' => array(
            'controller' => 'settingsController',
            'action' => 'viewCategory'
        )
    ),
    'settings-passphrase' => array(
        'path' => '/settings/passphrase/',
        'values' => array(
            'controller' => 'settingsController',
            'action' => 'passphraseSettings'
        )
    ),

);
