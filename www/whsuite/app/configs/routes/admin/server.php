<?php

$routes = array(
    // Server Management
    'server' => array(
        'path' => '/servers/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'listServers'
        )
    ),
    'servergroup-add' => array(
        'path' => '/servers/new-group/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'newGroup'
        )
    ),
    'servergroup-manage' => array(
        'path' => '/servers/group/{:id}/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'manageGroup'
        )
    ),
    'servergroup-delete' => array(
        'path' => '/servers/group/{:id}/delete/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'deleteGroup'
        )
    ),
    'server-add' => array(
        'path' => '/servers/group/{:id}/new-server/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'newServer'
        )
    ),
    'server-manage' => array(
        'params' => array(
            'server_id' => '(\d+)',
        ),
        'path' => '/servers/group/{:id}/server/{:server_id}/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'manageServer'
        )
    ),
    'server-manage-tab' => array(
        'params' => array(
            'server_id' => '(\d+)',
        ),
        'path' => '/servers/group/{:id}/server/{:server_id}/manage-tab/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'manageServerTab'
        )
    ),
    'server-delete' => array(
        'params' => array(
            'server_id' => '(\d+)',
        ),
        'path' => '/servers/group/{:id}/server/{:server_id}/delete/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'deleteServer'
        )
    ),
    'serverip-add' => array(
        'params' => array(
            'server_id' => '(\d+)',
        ),
        'path' => '/servers/group/{:id}/server/{:server_id}/new-ip-range/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'newIpRange'
        )
    ),
    'serverip-delete' => array(
        'params' => array(
            'server_id' => '(\d+)',
        ),
        'path' => '/servers/group/{:id}/server/{:server_id}/delete-ips/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'deleteIps'
        )
    ),

    'servernameserver-add' => array(
        'params' => array(
            'server_id' => '(\d+)',
        ),
        'path' => '/servers/group/{:id}/server/{:server_id}/new-nameserver/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'newNameserver'
        )
    ),
    'servernameserver-delete' => array(
        'params' => array(
            'server_id' => '(\d+)',
        ),
        'path' => '/servers/group/{:id}/server/{:server_id}/delete-nameservers/',
        'values' => array(
            'controller' => 'ServersController',
            'action' => 'deleteNameservers'
        )
    )
);