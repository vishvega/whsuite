<?php

$routes = array(
    // SERVICES (PURCHASES)

    'services' => array(
        'path' => '/services/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'listServices'
        )
    ),
    'services-paging' => array(
        'params' => array(
            'page' => '(\d+)'
        ),
        'path' => '/services/{:page}/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'listServices'
        )
    ),

    'client-service' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'manageService'
        )
    ),
    'hosting-decrypt-password' => array(
        'params' => array(
            'hosting_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/hosting/{:hosting_id}/password-decrypt/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'decryptHostingPassword'
        )
    ),
    'service-edit-details' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/edit-details/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'editServiceDetails'
        )
    ),
    'service-edit-ips' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/edit-ips/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'editServiceIps'
        )
    ),
    'service-add-addon' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/add-addon/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'addAddonForm'
        )
    ),
    'service-add-addon-save' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/add-addon/save/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'addAddon'
        )
    ),
    'service-manage-addon' => array(
        'params' => array(
            'service_id' => '(\d+)',
            'addon_purchase_id' => '(\d+)',
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/addon/{:addon_purchase_id}/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'manageAddon'
        )
    ),
    'service-delete-addon' => array(
        'params' => array(
            'service_id' => '(\d+)',
            'addon_purchase_id' => '(\d+)',
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/addon/{:addon_purchase_id}/delete/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'deleteAddon'
        )
    ),
    'service-domain-register' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/register-domain/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'registerDomain'
        )
    ),
    'service-domain-transfer' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/transfer-domain/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'transferDomain'
        )
    ),
    'service-domain-renew' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/renew-domain/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'renewDomain'
        )
    ),
    'service-domain-nameservers' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/nameservers/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'domainNameservers'
        )
    ),
    'service-domain-auth-code' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/auth-code/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'domainAuthCode'
        )
    ),
    'service-domain-contacts' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/domain-contacts/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'domainContacts'
        )
    ),
    'service-domain-lock' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/domain-lock/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'lockDomain'
        )
    ),
    'service-domain-unlock' => array(
        'params' => array(
            'service_id' => '(\d+)'
        ),
        'path' => '/client/profile/{:id}/service/{:service_id}/domain-unlock/',
        'values' => array(
            'controller' => 'ServiceController',
            'action' => 'unlockDomain'
        )
    ),

);