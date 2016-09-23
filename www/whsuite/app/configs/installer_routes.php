<?php

\App::get('router')->attach(
    '',
    array(
        'values' => array(
            'sub-folder' => 'installer'
        ),
        'name_prefix' => 'install-',
        'routes' => array(
            'start' => array(
                'path' => '/install/',
                'values' => array(
                    'controller' => 'InstallController',
                    'action' => 'extensions'
                )
            ),
            'database' => array(
                'path' => '/install/database/',
                'values' => array(
                    'controller' => 'InstallController',
                    'action' => 'configureDatabase'
                )
            ),
            'configure' => array(
                'path' => '/install/configure/',
                'values' => array(
                    'controller' => 'InstallController',
                    'action' => 'configureSystem'
                )
            ),
            'upgrade' => array(
                'path' => '/install/upgrade/',
                'values' => array(
                    'controller' => 'UpgradeController',
                    'action' => 'upgrade'
                )
            ),
            'finish' => array(
                'path' => '/install/finish/',
                'values' => array(
                    'controller' => 'InstallController',
                    'action' => 'finish'
                )
            )
        )
    )
);
