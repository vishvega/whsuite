<?php

$routes = array(
    /**
     * Homepage
     */
    'home' => array(
        'path' => '/',
        'values' => array(
            'controller' => 'HomeController',
            'action' => 'index'
        )
    ),

    'page-not-found' => array(
        'path' => '/page-not-found/',
        'values' => array(
            'controller' => 'ErrorsController',
            'action' => 'pageNotFound'
        )
    )
);
