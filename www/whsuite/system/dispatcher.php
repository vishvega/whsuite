<?php

namespace Core;

use Illuminate\Support\Str;
use App;

class Dispatcher
{
    /**
     * route we are currently dispatching to
     */
    protected $route = null;

    /**
     * path to controller we are loading
     */
    private $controller_path = null;

    /**
     * http library for 'inline' requests
     */
    protected $http = null;

    /**
     * constructor
     *
     * @param   object  object from router with the route to dispatch to
     */
    public function __construct($route)
    {
        if (is_object($route)) {

            $this->route = $route;
        }
    }

    /**
     * check the route
     * get the (addon), controller, function, params and see if it exists
     *
     * @return  bool
     */
    public function check()
    {
        $controller = $this->buildControllerPath($this->route->values);

        $this->controller_path = $controller;
        require_once($this->controller_path);

        // check the controller has the function we want
        $reflection = new \ReflectionClass($this->route->values['controller']);

        try {

            $method = $reflection->getMethod($this->route->values['action']);
        } catch (\ReflectionException $e) {

            throw new \Core\Exceptions\MethodNotFoundException($this->route->values);
        }

        return true;
    }


    /**
     * build file path
     *
     * @param   array   array of url parameters needed to generate controller path
     * @return  mixed   path to the controller we need to load or bool on fail
     */
    public function buildControllerPath($values)
    {
        // check to see if we have the main info we need
        if (empty($values['controller']) || empty($values['action'])) {
            throw new \Core\Exceptions\ControllerNotSpecifiedException;
        }

        // try building the file path
        $controller = '';

        // check if it's an addon
        if (! empty($values['addon'])) {

            $controller .= ADDON_DIR . DS . $values['addon'] . DS . 'controllers';
        } else {
            $controller .= APP_DIR . DS . 'controllers';
        }

        // check for sub-folder
        if (! empty($values['sub-folder'])) {

            $controller .= DS . $values['sub-folder'];
        }

        // add the controller name
        $controller .= DS . Str::snake($values['controller']) . '.php';

        if (file_exists($controller)) {

            return $controller;
        } else {
            throw new \Core\Exceptions\ControllerNotFoundException($controller);
        }
    }

    /**
     * we have a route! Dispatch
     *
     */
    public function run()
    {
        // final check to make sure controller was set (and consequently loaded in)
        if (empty($this->controller_path)) {

            App::stop('404');
        }

        // get the params
        $params = $this->route->values;
        unset(
            $params['sub-folder'],
            $params['controller'],
            $params['action'],
            $params['addon']
        );

        // load the reflection
        $reflection = new \ReflectionClass($this->route->values['controller']);
        $instance = $reflection->newInstance();

        $method_reflection = new \ReflectionMethod($instance, $this->route->values['action']);

        $method_reflection->invokeArgs($instance, $params);


        return;
    }

    /**
     * dispatch to the given route and return data
     *
     * @param   string  the url to dispatch to
     * @param   string  (optional) method type to use
     * @param   mixed   (optional) any data to set, can be string / array / file resource (only used in post/put method)
     * @param   mixed   (optional) authentication: array('type' => 'basic|digest|ntlm|any', 'username' => '', 'password' => '')
     *                  (see aura/http/src/aura/http/message/request.php for more info)
     * @return  mixed
     */
    public function load($url, $method = 'get', $data = array(), $auth = null)
    {
        $method = strtolower($method);

        $Http = new \Whsuite\Http\Http;

        $Request = $Http->newRequest();
        $Request->setTimeout(60);
        $Request->setUrl($url . $this->processParams($data));
        $Request->setMethod($method);

        // any data to set for post / put reqest
        if ($method == 'post' || $method == 'put') {

            $Request->setContent(json_encode($data));
        }

        // any auth needed?
        if (! empty($auth) && isset($auth['type'])) {

            $type = (! empty($auth['type'])) ? $auth['type'] : null;
            $Request->setAuth(
                $auth['username'],
                $auth['password'],
                $type
            );
        }

        // send the request
        $Response = $Http->send($Request);

        if ($Response->isSuccessful()) {
            return $Response->getBody();
        } else {
            return false;
        }
    }

    /**
     * process the route parameters and convert them into a uri string.
     *
     * @param   array   an array of the strings that need to be added to the uri
     * @param   bool    used to determin if a multidimentional array item has its own key value, or if it should fallback.
     * @param   bool    used to determin if the current array group is a child of the main array group.
     * @return  string
     */
    private function processParams($params, $custom_key = false, $child = false)
    {
        $uri_string = '';

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $uri_string .= $this->processParams($value, $key, true);
            } else {
                if ($custom_key) {
                    $uri_string .= $custom_key.'='.urlencode($value).'&';
                } else {
                    $uri_string .= $key.'='.urlencode($value).'&';
                }
            }
        }

        if (! $child) {
            $uri_string = rtrim($uri_string, '&');

            $uri_string = '?'.$uri_string;
        }

        return $uri_string;
    }

    /**
     * return the route we are loading
     *
     * @return  object  route object returned from the router for the route we are loading
     */
    public function getRoute()
    {
        return $this->route;
    }


    /**
     * return the page number
     *
     * @return  int  page number found in URL, if none exists '1' will be returned
     */
    public function getPageNumber()
    {
        $page_number = 1;
        if (isset($this->route->page_number) && $this->route->page_number > 0) {

            $page_number = $this->route->page_number;
        }

        return $page_number;
    }
}
