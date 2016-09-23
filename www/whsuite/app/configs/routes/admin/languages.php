<?php

$routes = array(

    /*
     * Language management routes
     */
    'language' => array(
        'path' => '/languages/',
        'values' => array(
            'controller' => 'LanguagesController',
            'action' => 'listing'
        )
    ),
    'language-import' => array(
        'path' => '/languages/import/',
        'values' => array(
            'controller' => 'LanguagesController',
            'action' => 'import'
        )
    ),
    'language-delete' => array(
        'path' => '/languages/delete-pack/{:id}/',
        'values' => array(
            'controller' => 'LanguagesController',
            'action' => 'deletePack'
        )
    )
);