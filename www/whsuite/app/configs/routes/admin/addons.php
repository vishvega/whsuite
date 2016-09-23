<?php

$routes = array(

    'addon' => array(
        'path' => '/addons/',
        'values' => array(
            'controller' => 'AddonController',
            'action' => 'index'
        )
    ),
    'addon-install' => array(
        'params' => array(
            'slug' => '([A-Za-z0-9_]+)'
        ),
        'path' => '/addons/{:slug}/install/',
        'values' => array(
            'controller' => 'AddonController',
            'action' => 'installAddon'
        )
    ),
    'addon-uninstall' => array(
        'path' => '/addons/{:id}/uninstall/',
        'values' => array(
            'controller' => 'AddonController',
            'action' => 'uninstallAddon'
        )
    ),
    'addon-enable' => array(
        'path' => '/addons/{:id}/enable/',
        'values' => array(
            'controller' => 'AddonController',
            'action' => 'enableAddon'
        )
    ),
    'addon-disable' => array(
        'path' => '/addons/{:id}/disable/',
        'values' => array(
            'controller' => 'AddonController',
            'action' => 'disableAddon'
        )
    ),
    'addon-manage' => array(
        'path' => '/addons/{:id}/',
        'values' => array(
            'controller' => 'AddonController',
            'action' => 'manageAddon'
        )
    ),
    'addon-update' => array(
        'path' => '/addons/{:id}/update/',
        'values' => array(
            'controller' => 'AddonController',
            'action' => 'updateAddon'
        )
    ),
);
