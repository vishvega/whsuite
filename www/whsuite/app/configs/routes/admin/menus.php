<?php

$routes = array(
    /**
     * Menu management routes
     */
    'menus' => array(
        'path' => '/menus/',
        'values' => array(
            'controller' => 'MenuController',
            'action' => 'listing'
        )
    ),
    'menu-add' => array(
        'path' => '/menus/add/',
        'values' => array(
            'controller' => 'MenuController',
            'action' => 'addMenu'
        )
    ),
    'menu-manage' => array(
        'path' => '/menus/{:id}/',
        'values' => array(
            'controller' => 'MenuController',
            'action' => 'manageMenu'
        )
    ),
    'menu-delete' => array(
        'path' => '/menus/{:id}/delete/',
        'values' => array(
            'controller' => 'MenuController',
            'action' => 'deleteMenu'
        )
    ),
    'menulink-add' => array(
        'path' => '/menus/{:id}/add-link/',
        'values' => array(
            'controller' => 'MenuController',
            'action' => 'addMenuLink'
        )
    ),
    'menulink-edit' => array(
        'params' => array(
            'link_id' => '(\d+)'
        ),
        'path' => '/menus/{:id}/edit-link/{:link_id}/',
        'values' => array(
            'controller' => 'MenuController',
            'action' => 'editMenuLink'
        )
    ),
    'menulink-delete' => array(
        'params' => array(
            'link_id' => '(\d+)'
        ),
        'path' => '/menus/{:id}/delete-link/{:link_id}/',
        'values' => array(
            'controller' => 'MenuController',
            'action' => 'deleteMenuLink'
        )
    )
);
