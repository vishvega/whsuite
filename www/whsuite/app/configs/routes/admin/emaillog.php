<?php

$routes = array(
    // EMAIL LOG
    'clientemail-add' => array(
        'path' => '/client/profile/{:id}/emails/compose/',
        'values' => array(
            'controller' => 'EmailController',
            'action' => 'composeEmail'
        )
    ),
    'clientemail' => array(
        'path' => '/client/profile/{:id}/emails/',
        'values' => array(
            'controller' => 'EmailController',
            'action' => 'clientEmails'
        )
    ),
    'clientemail-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/emails/{:page}/',
        'values' => array(
            'controller' => 'EmailController',
            'action' => 'clientEmails'
        )
    ),
    'clientemail-view' => array(
        'params' => array(
            'email_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/email/{:email_id}/',
        'values' => array(
            'controller' => 'EmailController',
            'action' => 'viewSentEmail'
        )
    ),
    'clientemail-view-body' => array(
        'params' => array(
            'email_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/email/{:email_id}/body/',
        'values' => array(
            'controller' => 'EmailController',
            'action' => 'viewEmailBody'
        )
    ),
    'clientemail-plaintext-preview' => array(
        'path' => '/emails/plaintext-preview/{:id}/',
        'values' => array(
            'controller' => 'EmailController',
            'action' => 'plaintextPreview'
        )
    ),
    'clientemail-resend' => array(
        'params' => array(
            'email_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/emails/resend/{:email_id}/',
        'values' => array(
            'controller' => 'EmailController',
            'action' => 'resendEmail'
        )
    ),
    'clientemail-delete' => array(
        'params' => array(
            'email_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/emails/delete/{:email_id}/',
        'values' => array(
            'controller' => 'EmailController',
            'action' => 'deleteEmail'
        )
    )
);