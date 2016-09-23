<?php

namespace Core;

use Aura\Router\Map;
use Aura\Router\DefinitionFactory;
use Aura\Router\RouteFactory;
use App;

class Router
{
    /**
     * Main Router Object
     *
     */
    protected $router = null;

    /**
     * constructor
     *
     * setup router instance and setup route files
     */
    public function __construct()
    {
        $this->router = new Map(new DefinitionFactory, new RouteFactory);
    }

    /**
     * register the main routes and then loop the installed addons
     * and register their routes (if any exist)
     */
    public function loadRoutes()
    {
        // check for main routes file
        $main_routes = APP_DIR . DS . 'configs' . DS . 'routes.php';
        if (file_exists($main_routes)) {
            include_once($main_routes);
        }

        // loop the installed addons
        $addons = App::installedAddons();

        foreach ($addons as $addon) {
            $addon_routes = ADDON_DIR . DS . $addon . DS . 'configs' . DS . 'routes.php';

            if (file_exists($addon_routes)) {
                include_once($addon_routes);
            }
        }
    }

    /**
     * call the router match to find a valid route.
     * then check for wildcard to process other vars
     *
     * Gets a route that matches a given path and other server conditions.
     *
     * @param string $path The path to match against.
     *
     * @param array $server An array copy of $_SERVER.
     *
     * @return Route|false Returns a Route object when it finds a match, or
     * boolean false if there is no match.
     *
     */
    public function match($path, array $server)
    {
        $route = $this->router->match($path, $server);
        if ($route === false) {
            return false;
        }

        // check for page number
        if (isset($route->values['extras']) && ! empty($route->values['extras'])) {
            foreach ($route->values['extras'] as $key => $extra) {
                if (strpos(strtolower($extra), 'page-') !== false) {
                    $route->page_number = substr($extra, 5);
                    break;
                }
            }
        }

        return $route;
    }

    /**
     * Add the URL_PREFIX to all attach calls
     *
     * Attaches several routes at once to a specific path prefix.
     *
     * @param string $path_prefix The path that the routes should be attached
     * to.
     *
     * @param array $spec An array of common route information, with an
     * additional `routes` key to define the routes themselves.
     *
     * @return void
     *
     */
    public function attach($path_prefix, $spec)
    {
        $path_prefix = URL_PREFIX . $path_prefix;

        $this->router->attach($path_prefix, $spec);
    }

    /**
     * generate the full url with domain name prefixed to route
     *
     * @param string $name The route name to look up.
     * @param array $data The data to interpolate into the URI; data keys
     * map to param tokens in the path.
     *
     * @return string|false A URI path string if the route name is found, or
     * boolean false if not.
     */
    public function fullUrlGenerate($name, $data = null)
    {
        $url = 'http';

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $url .= 's';
        }

        $url .= '://';

        if (! empty($_SERVER['HTTP_HOST'])) {
            $url .= $_SERVER['HTTP_HOST'];
        } elseif (getenv('HTTP_HOST') !== false) {
            $url .= getenv('HTTP_HOST');
        }

        try {
            $url .= $this->router->generate(
                $name,
                $data
            );
        } catch (\Exception $e) {
            return '';
        }

        return $url;
    }

    /**
     * function overloading to prevent having to use App::get('router')->router->add();
     * can just do App::get('router')->add();
     *
     * @param   string  method name we are trying to load
     * @param   array   array of params to pass to the method
     * @return   mixed   return of the method
     */
    public function __call($name, $params)
    {
        if (method_exists($this->router, $name)) {
            $method_reflection = new \ReflectionMethod($this->router, $name);

            return $method_reflection->invokeArgs($this->router, $params);

        } else {
            throw new \Exception('Fatal Error: Function '.$name.' does not exist!');
        }
    }
}
