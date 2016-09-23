<?php

$routes = array(
    // Custom Field Management
    'custom-fields' => array(
        'path' => '/custom-fields/',
        'values' => array(
            'controller' => 'CustomfieldsController',
            'action' => 'viewGroups'
        )
    ),
    'custom-fields-paging' => array(
        'params' => array(
            'page_id' => '(\d+)',
        ),
        'path' => '/custom-fields/{:page}/',
        'values' => array(
            'controller' => 'CustomfieldsController',
            'action' => 'viewGroups'
        )
    ),
    'custom-fields-view-group' => array(
        'path' => '/custom-fields/group/{:id}/',
        'values' => array(
            'controller' => 'CustomfieldsController',
            'action' => 'viewGroup'
        )
    ),
    'custom-fields-new-field' => array(
        'path' => '/custom-fields/group/{:id}/new-field/',
        'values' => array(
            'controller' => 'CustomfieldsController',
            'action' => 'newField'
        )
    ),
    'custom-fields-edit-field' => array(
        'params' => array(
            'field_id' => '(\d+)',
        ),
        'path' => '/custom-fields/group/{:id}/edit-field/{:field_id}/',
        'values' => array(
            'controller' => 'CustomfieldsController',
            'action' => 'editField'
        )
    ),
    'custom-fields-delete-field' => array(
        'params' => array(
            'field_id' => '(\d+)',
        ),
        'path' => '/custom-fields/group/{:id}/delete-field/{:field_id}/',
        'values' => array(
            'controller' => 'CustomfieldsController',
            'action' => 'deleteField'
        )
    ),
    'custom-fields-delete-group' => array(
        'params' => array(
            'field_id' => '(\d+)',
        ),
        'path' => '/custom-fields/group/{:id}/delete/',
        'values' => array(
            'controller' => 'CustomfieldsController',
            'action' => 'deleteGroup'
        )
    ),
    'custom-fields-new-group' => array(
        'params' => array(
            'field_id' => '(\d+)',
        ),
        'path' => '/custom-fields/group/new-group/',
        'values' => array(
            'controller' => 'CustomfieldsController',
            'action' => 'newGroup'
        )
    )
);