<?php

$routes = array(
    /**
     * Staff management routes
     */
    'search-results' => array(
        'path' => '/search/',
        'values' => array(
            'controller' => 'SearchController',
            'action' => 'results'
        )
    ),
    'search-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/search/{:page}/',
        'values' => array(
            'controller' => 'SearchController',
            'action' => 'results'
        )
    ),
);