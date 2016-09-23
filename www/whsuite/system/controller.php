<?php

namespace Core;

use App;

class Controller
{
    /**
     * storage for the view object
     */
    protected $view = null;

    /**
     * storage for the assets object
     */
    protected $assets = null;

    /**
     * constructor
     * setup the controller
     */
    final public function __construct()
    {
        // setup the view connection
        $this->view = &App::get('view');
        $this->view->set('view', $this->view);

        // setup the assets connection
        $this->assets = &App::get('assets');
        $this->view->set('assets', $this->assets);

        // setup the router connection
        $router = &App::get('router');
        $this->view->set('router', $router);

        // callback method for controller constructor
        if (method_exists($this, 'onLoad')) {

            $this->onLoad();
        }
    }
}
