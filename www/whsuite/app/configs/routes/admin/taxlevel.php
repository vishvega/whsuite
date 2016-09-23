<?php

$routes = array(
    /**
     * tax level management routes
     */
    'taxlevel' => array(
        'path' => '/tax-levels/',
        'values' => array(
            'controller' => 'TaxLevelsController',
            'action' => 'index'
        )
    ),
    'taxlevel-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/tax-levels/{:page}/',
        'values' => array(
            'controller' => 'TaxLevelsController',
            'action' => 'index'
        )
    ),
    'taxlevel-edit' => array(
        'path' => '/tax-levels/edit/{:id}/',
        'values' => array(
            'controller' => 'TaxLevelsController',
            'action' => 'form'
        )
    ),
    'taxlevel-add' => array(
        'path' => '/tax-levels/add/',
        'values' => array(
            'controller' => 'TaxLevelsController',
            'action' => 'form'
        )
    ),
    'taxlevel-delete' => array(
        'path' => '/tax-levels/delete/{:id}/',
        'values' => array(
            'controller' => 'TaxLevelsController',
            'action' => 'delete'
        )
    )
);