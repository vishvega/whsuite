<?php

namespace Core;

use Dflydev\DotAccessData\Data;
use Illuminate\Support\Str;

class Configs
{
    /**
     * array of loaded configs
     */
    protected $configs = null;

    /**
     * array of config files to ingore
     * usually because other classes will be dealing with them
     */
    protected $ignore_list = array(
        'routes.php',
        'hooks.php',
        '.ds_store'
    );

    /**
     * constructor
     * start up and load in the config directory and "register" the files
     *
     * @param   string  path to the default app config directory
     */
    public function __construct($config_dir = '')
    {
        $this->configs = new Data;
        $this->registerDir($config_dir);
    }

    /**
     * register a new config file
     *
     * @param   string  path to the config to register
     * @return  bool
     */
    public function register($config)
    {
        if (file_exists($config)) {
            $path_bits = explode(DS, $config);
            $filename = strtolower(substr(end($path_bits), 0, -4));

            if (strpos($config, ADDON_DIR) !== false) {
                $addon = str_replace(ADDON_DIR . DS, '', $config);
                $addon = str_replace(DS . 'configs' . DS . end($path_bits), '', $addon);

                $this->configs->set($addon . '.' . $filename, $config);
            } else {
                $this->configs->set($filename, $config);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * register a directory of config files
     *
     * @param   string  path to the directory to scan
     * @return  bool
     */
    public function registerDir($directory)
    {
        if (file_exists($directory)) {
            // loop the directory to "register" the configs
            $dir = new \DirectoryIterator($directory);

            if (Str::endsWith($directory, array('/', '\\'))) {
                $directory = substr($directory, 0, -1);
            }

            foreach ($dir as $file_info) {
                if ($file_info->isDot() || in_array($file_info->getFilename(), $this->ignore_list)) {
                    continue;
                }

                $this->register($directory . DS . $file_info->getFilename());
            }

            return true;

        } else {
            return false;
        }
    }

    /**
     * get a config array or item
     *
     * @param   string  config file or item to get in dot notation form
     * @return  mixed   requested config file or item OR null on not found
     */
    public function get($key)
    {
        $result = $this->processKey($key);

        if ($result === false) {
            return null;
        }

        // retrieve data originally required
        $key = str_replace('::', '.', $key);

        return $this->configs->get($key);
    }

    /**
     * set a new item to the config
     *
     * @param   string  dot notation 'key' to assign the value to
     * @param   mixed   value to assign to the 'key'
     * @param   bool    (optional) bool on whether to overwrite value if already exists
     * @return  bool
     */
    public function set($key, $value, $overwrite = true)
    {
        $result = $this->processKey($key);

        $check = $this->configs->get($key);

        if (! is_null($check) && $overwrite === false) {
            return false;
        } else {
            // retrieve data originally required
            $key = str_replace('::', '.', $key);

            $this->configs->set($key, $value);
            return true;
        }
    }

    /**
     * export all the current stored config items
     *
     * @return  array   all the currently stored items in the data array
     */
    public function export()
    {
        return $this->configs->export();
    }

    /**
     * given the sub directory of the routes folder, load all the given routes.
     *
     * @param string routes sub-folder to look in for the routes
     * @return array array of all routes to pass to aura router
     */
    public function getRoutes($folder)
    {
        $return_array = array();

        // check we can get the directory we called this function from
        $backtrace = debug_backtrace();
        if (! isset($backtrace['0']['file'])) {
            return $return_array;
        }

        $bits = explode(DS, $backtrace['0']['file']);
        array_pop($bits);

        $route_dir = implode(DS, $bits) . DS . 'routes' . DS . $folder;

        // check the directory exists
        if (! file_exists($route_dir)) {
            return $return_array;
        }

        // loop the directory and get routes
        $dir = new \DirectoryIterator($route_dir);

        foreach ($dir as $file_info) {
            if ($file_info->isDot() || in_array(strtolower($file_info->getFilename()), $this->ignore_list)) {
                continue;
            }
            unset($routes); // unset the routes variable so we can check it's been imported again

            include_once($route_dir . DS . $file_info->getFilename());

            if (isset($routes) && is_array($routes)) {
                $return_array = $return_array + $routes;
            }
        }

        return $return_array;
    }

    /**
     * get config item key and then load the file if not already loaded (if exists)
     *
     * @param   string  config item to load
     * @param   bool    whether or not we processed the key successfully
     */
    protected function processKey($config_item)
    {
        if (empty($config_item)) {
            return false;
        }

        // check to see if it's an addons config
        if (strpos($config_item, '::') !== false) {
            $addon = explode('::', $config_item);
            $bits = explode('.', $addon['1']);

            // setup name to retrieve
            $config_file_key = $addon['0'] . '.' . $bits['0'];
        } else {
            // it's not
            $bits = explode('.', $config_item);

            // setup name to retrieve
            $config_file_key = $bits['0'];
        }

        // attempt to get the main file
        $config_file = $this->configs->get($config_file_key);

        // doesn't exist
        if (is_null($config_file)) {
            return false;
        }

        // exists but it's not an array, load file from file path
        if (! empty($config_file) && ! is_array($config_file) && file_exists($config_file)) {
            $this->loadConfig($config_file_key);
        }

        return true;
    }

    /**
     * protected method to load in a registered config
     *
     * @param   string  key of the config to load in
     */
    protected function loadConfig($key)
    {
        $config = include_once($this->configs->get($key));

        if (is_array($config)) {
            $this->configs->set($key, $config);
        }
    }
}
