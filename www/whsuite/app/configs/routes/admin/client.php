<?php

$routes = array(
    /**
     * Client Management Routes
     */

    'client' => array(
        'path' => '/clients/',
        'values' => array(
            'controller' => 'ClientsController',
            'action' => 'index'
        )
    ),
    'client-paging' => array(
        'params' => array(
            'page' => '(\d+)',
        ),
        'path' => '/clients/{:page}/',
        'values' => array(
            'controller' => 'ClientsController',
            'action' => 'index'
        )
    ),
    'client-profile' => array(
        'path' => '/client/profile/{:id}/',
        'values' => array(
            'controller' => 'ClientsController',
            'action' => 'clientProfile'
        )
    ),
    'client-activate' => array(
        'path' => '/client/activate/{:id}/',
        'values' => array(
            'controller' => 'ClientsController',
            'action' => 'clientActivate'
        )
    ),
    'client-edit' => array(
        'path' => '/client/edit/{:id}/',
        'values' => array(
            'controller' => 'ClientsController',
            'action' => 'clientEdit'
        )
    ),
    'client-add' => array(
        'path' => '/client/add/',
        'values' => array(
            'controller' => 'ClientsController',
            'action' => 'addClient'
        )
    ),

    'client-new-invoice' => array(
        'path' => '/client/profile/{:id}/create-invoice/',
        'values' => array(
            'controller' => 'InvoiceController',
            'action' => 'createInvoice'
        )
    ),


    'client-new-order' => array(
        'path' => '/client/profile/{:id}/new-order/',
        'values' => array(
            'controller' => 'OrdersController',
            'action' => 'newOrder'
        )
    ),

    'client-services' => array(
        'path' => '/client/profile/{:id}/services/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'listServices'
        )
    ),
    'client-services-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/services/{:page}/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'listServices'
        )
    ),

    // CLIENT NOTES
    'clientnote' => array(
        'path' => '/client/profile/{:id}/notes/',
        'values' => array(
            'controller' => 'ClientNotesController',
            'action' => 'listNotes'
        )
    ),
    'clientnote-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/notes/{:page}/',
        'values' => array(
            'controller' => 'ClientNotesController',
            'action' => 'listNotes'
        )
    ),
    'clientnote-add' => array(
        'path' => '/client/profile/{:id}/notes/add/',
        'values' => array(
            'controller' => 'ClientNotesController',
            'action' => 'notes_form'
        )
    ),
    'clientnote-edit' => array(
        'params' => array(
            'note' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/notes/edit/{:note_id}/',
        'values' => array(
            'controller' => 'ClientNotesController',
            'action' => 'notes_form'
        )
    ),
    'clientnote-delete' => array(
        'params' => array(
            'note_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/notes/delete/{:note_id}/',
        'values' => array(
            'controller' => 'ClientNotesController',
            'action' => 'notes_delete'
        )
    ),

    // CLIENT MISC
    'client-email-new-password' => array(
        'path' => '/client/profile/{:id}/email-password/',
        'values' => array(
            'controller' => 'ClientsController',
            'action' => 'emailPassword'
        )
    ),
    'client-login' => array(
        'path' => '/client/profile/{:id}/login/',
        'values' => array(
            'controller' => 'ClientsController',
            'action' => 'loginAsClient'
        )
    )
);
