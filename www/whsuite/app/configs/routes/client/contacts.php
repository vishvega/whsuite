<?php

$routes = array(

    'contacts' => array(
        'path' => '/contacts/',
        'values' => array(
            'controller' => 'ContactController',
            'action' => 'listContacts'
        )
    ),
    'contacts-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/contacts/{:page}/',
        'values' => array(
            'controller' => 'ContactController',
            'action' => 'index'
        )
    ),
    'manage-contact' => array(
        'path' => '/contact/{:contact_id}/',
        'values' => array(
            'controller' => 'ContactController',
            'action' => 'manageContact'
        )
    ),
    'delete-contact' => array(
        'path' => '/contact/{:contact_id}/delete/',
        'values' => array(
            'controller' => 'ContactController',
            'action' => 'deleteContact'
        )
    ),
    'create-contact' => array(
        'path' => '/contacts/add/',
        'values' => array(
            'controller' => 'ContactController',
            'action' => 'addContact'
        )
    ),
);
