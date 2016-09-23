<?php

$routes = array(

    'announcement' => array(
        'path' => '/announcements/',
        'values' => array(
            'controller' => 'AnnouncementController',
            'action' => 'index'
        )
    ),
    'announcement-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/announcements/{:page}/',
        'values' => array(
            'controller' => 'AnnouncementController',
            'action' => 'index'
        )
    ),
    'announcement-add' => array(
        'path' => '/announcements/add/',
        'values' => array(
            'controller' => 'AnnouncementController',
            'action' => 'form'
        )
    ),
    'announcement-edit' => array(
        'path' => '/announcements/edit/{:id}/',
        'values' => array(
            'controller' => 'AnnouncementController',
            'action' => 'form'
        )
    ),
    'announcement-delete' => array(
        'path' => '/announcements/delete/{:id}/',
        'values' => array(
            'controller' => 'AnnouncementController',
            'action' => 'delete'
        )
    )
);
