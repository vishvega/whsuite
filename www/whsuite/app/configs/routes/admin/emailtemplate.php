<?php

$routes = array(
	/**
	 * email templates
	 */
    'emailtemplate' => array(
        'path' => '/email-templates/',
        'values' => array(
            'controller' => 'EmailTemplatesController',
            'action' => 'index'
        )
    ),
    'emailtemplate-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/email-templates/{:page}/',
        'values' => array(
            'controller' => 'EmailTemplatesController',
            'action' => 'index'
        )
    ),
    'emailtemplate-edit' => array(
        'path' => '/email-templates/edit/{:id}/',
        'values' => array(
            'controller' => 'EmailTemplatesController',
            'action' => 'form'
        )
    ),
    'emailtemplate-add' => array(
        'path' => '/email-templates/add/',
        'values' => array(
            'controller' => 'EmailTemplatesController',
            'action' => 'form'
        )
    ),
    'emailtemplate-delete' => array(
        'path' => '/email-templates/delete/{:id}/',
        'values' => array(
            'controller' => 'EmailTemplatesController',
            'action' => 'delete'
        )
    ),


);