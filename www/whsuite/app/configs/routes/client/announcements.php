<?php

$routes = array(
    /**
     * Homepage
     */
    'announcements' => array(
        'path' => '/announcements/',
        'values' => array(
            'controller' => 'AnnouncementController',
            'action' => 'index'
        )
    ),
    'announcements-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/announcements/{:page}/',
        'values' => array(
            'controller' => 'AnnouncementController',
            'action' => 'index'
        )
    ),
    'announcement' => array(
        'path' => '/view-announcement/{:id}/',
        'values' => array(
            'controller' => 'AnnouncementController',
            'action' => 'viewAnnouncement'
        )
    )
);
