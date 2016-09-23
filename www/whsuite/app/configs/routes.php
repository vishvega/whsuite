<?php
/**
 * Routes Configuration
 *
 * This files stores all the routes for the core WHSuite system.
 *
 * @package  WHSuite-Configs
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */

/**
 * Admin Routes
 */
$admin_routes = App::get('configs')->getRoutes('admin');

App::get('router')->attach('/admin', array(
    'name_prefix' => 'admin-',
    'values' => array(
        'sub-folder' => 'admin'
    ),
    'params' => array(
        'id' => '(\d+)'
    ),

    'routes' => $admin_routes
));

/**
 * Client Routes
 */
$client_routes = App::get('configs')->getRoutes('client');

App::get('router')->attach('', array(
    'name_prefix' => 'client-',
    'values' => array(
        'sub-folder' => 'client'
    ),
    'params' => array(
        'id' => '(\d+)'
    ),

    'routes' => $client_routes
));

/**
 * Frontend/Client routes
 */


/**
 * Misc Routes
 */
App::get('router')->attach('', array(
    'params' => array(
        'id' => '(\d+)'
    ),
    'routes' => array(
        'automation' => array(
            'path' => '/automation/',
            'values' => array(
                'sub-folder' => 'admin',
                'controller' => 'AutomationController',
                'action' => 'runAutomation'
            )
        ),

    )
));
