<?php

$routes = array(
    /**
     * Dashboard
     */
    'home' => array(
        'path' => '/',
        'values' => array(
            'controller' => 'DashboardController',
            'action' => 'index'
        )
    )
);