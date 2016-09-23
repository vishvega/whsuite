<?php

namespace Core\ExceptionHandlers;

use \Whoops\Handler\Handler;

class Base extends Handler
{
    /**
     * @return int|null A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle()
    {
        \App::get('view')->set('error_message', $this->getException()->getMessage());
        $errorDispatch = new \stdClass; // start fake route class
        $errorDispatch->name = 'fake-error';

        // try to work out where to redirect error message to
        if (! empty($this->getException()->missingUrl)) {
            $route = $this->getException()->missingUrl;

            if (strpos($route, '/admin') === 0) {
                // set admin error controller
                $errorDispatch->values = array(
                    'addon' => false,
                    'sub-folder' => 'admin',
                    'controller' => 'ErrorsController',
                    'action' => 'index'
                );
            } else {
                // set client error controller
                $errorDispatch->values = array(
                    'addon' => false,
                    'sub-folder' => 'client',
                    'controller' => 'ErrorsController',
                    'action' => 'index'
                );
            }
        } else {
            $route = \App::get('dispatcher')->getRoute();
            $subFolder = $route->values['sub-folder'];

            if (! empty($subFolder) && $subFolder == 'admin') {
                // set admin error controller
                $errorDispatch->values = array(
                    'addon' => false,
                    'sub-folder' => 'admin',
                    'controller' => 'ErrorsController',
                    'action' => 'index'
                );
            } else {
                // set client error controller
                $errorDispatch->values = array(
                    'addon' => false,
                    'sub-folder' => 'client',
                    'controller' => 'ErrorsController',
                    'action' => 'index'
                );
            }
        }

        if (! empty($errorDispatch)) {
            // dispatch to controller
            if (\App::check('dispatcher')) {
                \App::get('dispatcher')->setRoute($errorDispatch);
            } else {
                \App::factory('\Core\Dispatcher', $errorDispatch)->check();
            }

            $this->bootApp();

            \App::get('hooks')->callListeners('pre_dispatch');

            \App::get('dispatcher')->run();

            \App::get('hooks')->callListeners('post_dispatch');

            \App::stop();
        } else {
            \App::get('view')->systemDisplay('errors/system_error.php');
        }

        return Handler::QUIT;
    }

    /**
     * load the app bootstrap so we can
     * show the error pages
     */
    public function bootApp()
    {
        if (file_exists(APP_DIR . DS . 'bootstrap.php')) {
            require_once(APP_DIR . DS . 'bootstrap.php');
        }
    }
}
