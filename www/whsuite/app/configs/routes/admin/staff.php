<?php

$routes = array(
    /**
     * Staff management routes
     */
    'staff' => array(
        'path' => '/staff/',
        'values' => array(
            'controller' => 'StaffsController',
            'action' => 'index'
        )
    ),
    'staff-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/staff/{:page}/',
        'values' => array(
            'controller' => 'StaffsController',
            'action' => 'index'
        )
    ),
    'staff-edit' => array(
        'path' => '/staff/edit/{:id}/',
        'values' => array(
            'controller' => 'StaffsController',
            'action' => 'form'
        )
    ),
    'staff-add' => array(
        'path' => '/staff/add/',
        'values' => array(
            'controller' => 'StaffsController',
            'action' => 'form'
        )
    ),
    'staff-delete' => array(
        'path' => '/staff/delete/{:id}/',
        'values' => array(
            'controller' => 'StaffsController',
            'action' => 'delete'
        )
    ),
    'staff-myprofile' => array(
        'path' => '/staff/my-profile/',
        'values' => array(
            'controller' => 'StaffsController',
            'action' => 'profile'
        )
    ),
);