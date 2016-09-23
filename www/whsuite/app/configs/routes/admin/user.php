<?php

$routes = array(
    /**
     * User related routes
     */
    'logout' => array(
        'path' => '/logout/',
        'values' => array(
            'controller' => 'LoginController',
            'action' => 'logout'
        )
    ),
    'login' => array(
        'path' => '/login/',
        'values' => array(
            'controller' => 'LoginController',
            'action' => 'index'
        )
    ),
    'forgotten-password' => array(
        'path' => '/forgotten-password/',
        'values' => array(
            'controller' => 'LoginController',
            'action' => 'forgottenPassword'
        )
    ),
    'reset-password' => array(
        'params' => array(
            'user_id' => '(\d+)',
            'reset_key' => '([A-Za-z0-9]+)'
        ),
        'path' => '/reset-password/{:user_id}/{:reset_key}/',
        'values' => array(
            'controller' => 'LoginController',
            'action' => 'resetPassword'
        )
    )
);