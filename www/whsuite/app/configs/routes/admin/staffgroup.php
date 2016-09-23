<?php

$routes = array(
    /**
     * Staff Group management routes
     */
    'staffgroup' => array(
        'path' => '/staff-groups/',
        'values' => array(
            'controller' => 'StaffGroupsController',
            'action' => 'index'
        )
    ),
    'staffgroup-add' => array(
        'path' => '/staff-groups/add/',
        'values' => array(
            'controller' => 'StaffGroupsController',
            'action' => 'form'
        )
    ),
    'staffgroup-edit' => array(
        'path' => '/staff-groups/edit/{:id}/',
        'values' => array(
            'controller' => 'StaffGroupsController',
            'action' => 'form'
        )
    ),
    'staffgroup-delete' => array(
        'path' => '/staff-groups/delete/{:id}/',
        'values' => array(
            'controller' => 'StaffGroupsController',
            'action' => 'delete'
        )
    )
);