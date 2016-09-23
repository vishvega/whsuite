<?php

$routes = array(
    /**
     * Dashboard Widgets
     */
    'widget-orders-recent-orders' => array(
        'path' => '/widgets/orders/recent/',
        'values' => array(
            'controller' => 'WidgetsController',
            'action' => 'recentOrders'
        )
    )
);