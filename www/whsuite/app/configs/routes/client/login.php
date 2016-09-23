<?php

$routes = array(
    'login' => array(
        'path' => '/login/',
        'values' => array(
            'controller' => 'LoginController',
            'action' => 'index'
        )
    ),
    'logout' => array(
        'path' => '/logout/',
        'values' => array(
            'controller' => 'LoginController',
            'action' => 'logout'
        )
    ),
    'forgot-password' => array(
        'path' => '/login/forgotten-password/',
        'values' => array(
            'controller' => 'LoginController',
            'action' => 'forgottenPassword'
        )
    ),
    'reset-password' => array(
        'params' => array(
            'reset_key' => '([A-Za-z0-9]+)'
        ),
        'path' => '/reset-password/{:id}/{:reset_key}/',
        'values' => array(
            'controller' => 'LoginController',
            'action' => 'resetPassword'
        )
    ),
    'create-account' => array(
        'path' => '/create-account/',
        'values' => array(
            'controller' => 'LoginController',
            'action' => 'createAccount'
        )
    ),
);
