<?php

$routes = array(

    /*
     * error routes
     */
    'notFound' => array(
        'path' => '/page-not-found/',
        'values' => array(
            'controller' => 'ErrorsController',
            'action' => 'index'
        )
    )

);
