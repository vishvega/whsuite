<?php

$routes = array(
    // Product Management
    'product' => array(
        'path' => '/products/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'listProducts'
        )
    ),
    'productgroup-add' => array(
        'path' => '/products/new-group/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'newGroup'
        )
    ),
    'productgroup-manage' => array(
        'path' => '/products/group/{:id}/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'manageGroup'
        )
    ),
    'productgroup-delete' => array(
        'path' => '/products/group/{:id}/delete/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'deleteGroup'
        )
    ),
    'product-add' => array(
        'path' => '/products/group/{:id}/new-product/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'newProduct'
        )
    ),
    'products-product-addon-fields' => array(
        'path' => '/products/addon-fields/{:id}/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'addonFields'
        )
    ),
    'products-product-registrar-fields' => array(
        'path' => '/products/registrar-fields/{:id}/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'registrarFields'
        )
    ),
    'products-product-domain-pricing' => array(
        'path' => '/products/domain-pricing/{:id}/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'domainPricing'
        )
    ),
    'product-manage' => array(
        'params' => array(
            'product_id' => '(\d+)',
        ),
        'path' => '/products/group/{:id}/product/{:product_id}/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'manageProduct'
        )
    ),
    'product-addon-fields' => array(
        'params' => array(
            'group_id' => '(\d+)',
        ),
        'path' => '/products/group/addon-fields/{:group_id}/{:product_id}/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'addonFields'
        )
    ),
    'product-delete' => array(
        'params' => array(
            'product_id' => '(\d+)',
        ),
        'path' => '/products/group/{:id}/product/{:product_id}/delete/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'deleteProduct'
        )
    ),

    'products-types' => array(
        'path' => '/products/types/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'listProductTypes'
        )
    ),

    'products-types-new-type' => array(
        'path' => '/products/types/new-type/',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'newProductType'
        )
    ),

    'products-types-manage' => array(
        'path' => '/products/types/{:id}',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'manageProductType'
        )
    ),

    'products-types-delete' => array(
        'path' => '/products/types/{:id}/delete',
        'values' => array(
            'controller' => 'ProductsController',
            'action' => 'deleteProductType'
        )
    ),

    'productaddon' => array(
        'params' => array(
            'product_id' => '(\d+)',
        ),
        'path' => '/product-addons/',
        'values' => array(
            'controller' => 'ProductAddonsController',
            'action' => 'index'
        )
    ),

    'productaddon-add' => array(
        'path' => '/product-addons/add/',
        'values' => array(
            'controller' => 'ProductAddonsController',
            'action' => 'form'
        )
    ),

    'productaddon-edit' => array(
        'path' => '/product-addons/edit/{:id}/',
        'values' => array(
            'controller' => 'ProductAddonsController',
            'action' => 'form'
        )
    ),

    'productaddon-delete' => array(
        'path' => '/product-addons/delete/{:id}/',
        'values' => array(
            'controller' => 'ProductAddonsController',
            'action' => 'delete'
        )
    )
);