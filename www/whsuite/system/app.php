<?php

class App
{
    /**
     * registry array
     *
     * @static
     * @var array
     */
    protected static $registry = array();

    /**
     * array containing the registered addons
     *
     * @static
     * @var array
     */
    protected static $addons = array();

    /**
     * start the app
     * and setup the autoloading of system classes
     *
     */
    public static function start()
    {
        // include the system autoloader
        if (file_exists(SYS_DIR . DS . 'autoloader.php')) {
            require_once(SYS_DIR . DS . 'autoloader.php');
        } else {
            die("Fatal Error: During start, autoloader not found!");
        }

        // init the autoloader
        self::factory('\Core\Autoloader');

        // add the exception handler
        $whoops = new \Whoops\Run;

        if (defined('DEV_MODE') && DEV_MODE) {
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        } else {
            $whoops->pushHandler(new \Core\ExceptionHandlers\Base);
        }

        $whoops->register();
    }

    /**
     * stop the app
     *
     * @param   string  (optional) the error
     */
    public static function stop($error = false)
    {
        if (App::check('hooks')) {
            App::get('hooks')->callListeners('app_shutdown', $error);
        }

        die();
    }

    /**
     * load the given class and add to App
     * will be added with the key of lowercase classname
     *
     * @param   string  class to load (including namespaces)
     * @param   mixed   optional data to pass to class constructor
     * @return  mixed   bool on false, object on load
     */
    public static function factory()
    {
        $params = func_get_args();

        if (! isset($params['0']) || empty($params['0'])) {
            return false;
        }

        $class_name = array_shift($params);

        // instantiate the class
        $reflection_class = new ReflectionClass($class_name);
        $instance = $reflection_class->newInstanceArgs($params);

        if ($instance instanceof $class_name) {
            // get the class name so we can
            $class = strtolower($reflection_class->getShortName());

            self::add($class, $instance);

            return self::get($class);
        } else {
            return false;
        }
    }

    /**
     * register and load the hooks config for app
     *
     */
    public static function hooks()
    {
        if (file_exists(APP_DIR . DS . 'configs' .DS . 'hooks.php')) {
            include(APP_DIR . DS . 'configs' .DS . 'hooks.php');
        }
    }

    /**
     * register the addons if they are installed
     * register the configs for each module if configs exist
     *
     */
    public static function registerAddons()
    {
        if (file_exists(ADDON_DIR)) {
            // loop the directory to "register" the configs
            $addons = new \DirectoryIterator(ADDON_DIR);

            $activeAddons = \Addon::active();
            $installedAddons = array();
            foreach ($activeAddons as $Addon) {
                $installedAddons[] = $Addon->directory;
            }

            foreach ($addons as $dir) {
                $addonName = $dir->getBasename();
                $isInstalled = in_array($addonName, $installedAddons);

                if ($dir->isDot() || $dir->isDir() === false || ! $isInstalled) {
                    continue;
                }

                self::$addons[] = $addonName;

                $addonCameled = \Illuminate\Support\Str::studly($addonName);
                $detailsFile = '\\Addon\\' . $addonCameled . '\\' . $addonCameled . 'Details';

                $detailsFile::bootstrap($addonName);
            }
        }
    }

    /**
     * add item to the registry
     *
     * @static
     * @param string - identifying key for retrieval later on
     * @param mixed - item to add to the registry
     * @param bool - overwrite item if key is already taken? (optional:default = true)
     * @return bool - usually true (unless $overwrite = false and $key already exists or $key is empty)
     */
    public static function add($key, $item, $overwrite = true)
    {
        if (empty($key)) {
            return false;
        } else {
            // add to the main registry

            if ($overwrite === false && isset(self::$registry[$key])) {
                return false;
            } else {
                self::$registry[$key] = $item;
            }
        }

        return true;
    }

    /**
     * retrieve an object from the registry
     *
     * @static
     * @param string - key to find and retrieve
     * @return mixed - the item stored / null if it's not set
     */
    public static function &get($key)
    {
        if (isset(self::$registry[$key])) {
            return self::$registry[$key];
        } else {
            return null;
        }
    }

    /**
     * check to see if an object exists in the registry
     *
     * @static
     * @param string - key to find and retrieve
     * @return bool
     */
    public static function check($key)
    {
        if (isset(self::$registry[$key])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * export the registry
     *
     * @return array - all the items currently assigned
     */
    public static function export()
    {
        return self::$registry;
    }

    /**
     * export list of installed addons
     *
     * @return array    all the installed addons
     */
    public static function installedAddons()
    {
        return self::$addons;
    }

    /**
     * check whather addon is installed
     *
     * @param   string  addon name
     * @return  bool    whether addon is installed
     */
    public static function checkInstalledAddon($addonName)
    {
        $addons = self::installedAddons();

        if (in_array($addonName, $addons)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * redirect method
     *
     * @param string $route Either the Aura Route name or url
     * @param array $params Optional params needed for a route
     * @param int $code Define optional status code
     *
     * @return void
     */
    public static function redirect($route, $params = array(), $code = 302)
    {
        if (strpos(strtolower($route), 'http') !== 0) {
            // route
            $redirectUrl = self::get('router')->generate(
                $route,
                $params
            );
        } else {
            $redirectUrl = $route;
        }

        $Http = new \Whsuite\Http\Http;
        $Response = $Http->newResponse();

        $Response->setStatusCode($code);
        $Response->setHeaders(
            array(
                'Location' => $redirectUrl
            )
        );

        $Response->setContent('Redirecting to <a href="' . $redirectUrl . '">' . $redirectUrl . '</a>');

        $Http->send($Response);
        exit;
    }

    /**
     * full url redirect method
     *
     * @param string $route Aura Route name
     * @param array $params Optional params needed for a route
     * @param int $code Define optional status code
     *
     * @return void
     */
    public static function fullUrlRedirect($route, $params = array(), $code = 302)
    {
        $redirectUrl = self::get('router')->fullUrlGenerate(
            $route,
            $params
        );

        self::redirect($redirectUrl, array(), $code);
        exit;
    }
}
