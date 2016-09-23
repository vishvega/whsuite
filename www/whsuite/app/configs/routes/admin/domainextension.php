<?php

$routes = array(

    /**
     * domain extensions
     */
    'domainextension' => array(
        'path' => '/domain-extensions/',
        'values' => array(
            'controller' => 'DomainExtensionsController',
            'action' => 'index'
        )
    ),
    'domainextension-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/domain-extensions/{:page}/',
        'values' => array(
            'controller' => 'DomainExtensionsController',
            'action' => 'index'
        )
    ),
    'domainextension-edit' => array(
        'path' => '/domain-extensions/edit/{:id}/',
        'values' => array(
            'controller' => 'DomainExtensionsController',
            'action' => 'form'
        )
    ),
    'domainextension-add' => array(
        'path' => '/domain-extensions/add/',
        'values' => array(
            'controller' => 'DomainExtensionsController',
            'action' => 'form'
        )
    ),
    'domainextension-delete' => array(
        'path' => '/domain-extensions/delete/{:id}/',
        'values' => array(
            'controller' => 'DomainExtensionsController',
            'action' => 'delete'
        )
    )
);