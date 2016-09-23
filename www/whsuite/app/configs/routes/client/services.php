<?php

$routes = array(
    /**
     * Homepage
     */
    'services' => array(
        'path' => '/services/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'index'
        )
    ),
    'services-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/services/{:page}/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'index'
        )
    ),
    'manage-service' => array(
        'path' => '/service/{:service_id}/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'manageService'
        )
    ),
    'service-manage-domain' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/service/{:service_id}/manage-domain/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'manageDomain'
        )
    ),
    'service-domain-nameservers' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/service/{:service_id}/nameservers/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'domainNameservers'
        )
    ),
    'service-domain-auth-code' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/service/{:service_id}/auth-code/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'domainAuthCode'
        )
    ),
    'service-domain-contacts' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/service/{:service_id}/domain-contacts/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'domainContacts'
        )
    ),
    'service-domain-lock' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/service/{:service_id}/domain-lock/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'lockDomain'
        )
    ),
    'service-domain-unlock' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/service/{:service_id}/domain-unlock/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'unlockDomain'
        )
    ),
);
