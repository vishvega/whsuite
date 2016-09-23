<?php

namespace Core;

use Illuminate\Support\Str;
use App;

class Autoloader
{
    /**
     * array of paths to class locations
     */
    protected $paths = array();

    /**
     * array of models + location for autoloading
     */
    protected $models = array();

    /**
     * register the autoloader
     */
    public function __construct()
    {
        $this->paths = array(
            'core'  => SYS_DIR,
            'app'   => APP_DIR,
            'addon' => APP_DIR . DS . 'addons'
        );

        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * load the class
     *
     * @param string $class_name The name of the class to load.
     * @return void
     */
    public function loadClass($class_name)
    {
        // check to see if it's the model array
        if (isset($this->models[$class_name])) {
            require_once($this->models[$class_name]);
        } else {
            // check what namespace we are dealing with
            // core?
            if (substr($class_name, 0, 5) == 'Core\\') {
                $class_name = substr($class_name, 5);
                $path = $this->paths['core'];

            } elseif (substr($class_name, 0, 4) == 'App\\') {
                // app?

                $class_name = substr($class_name, 4);
                $path = $this->paths['app'];

            } elseif (substr($class_name, 0, 6) == 'Addon\\') {
                // a addon?

                $class_name = substr($class_name, 6);
                $path = $this->paths['addon'];

            } elseif (strpos($class_name, 'Controller') !== false) {
                // base controller?

                $path = $this->paths['app'] . DS . 'controllers' . DS . 'base';
            }

            if (! isset($path)) {
                return false;
            }

            // convert rest to folder paths
            $class_name = str_replace('\\', DS, $class_name);

            // pop off the actual class so we can convert underscores to DS
            // TODO: see if there's a better way to do this
            $class_bits = explode(DS, $class_name);
            $class_bits = array_map('Illuminate\Support\Str::snake', $class_bits);

            $class = array_pop($class_bits);

            if (! empty($class_bits)) {
                $class_bits = implode(DS, $class_bits) . DS;
            } else {
                $class_bits = '';
            }

            // rebuild and check it exists
            $class_to_load = strtolower($path . DS . $class_bits . $class . '.php');

            if (file_exists($class_to_load)) {
                require_once($class_to_load);
            }
        }
    }

    /**
     * register all the system models and addon models
     *
     */
    public function registerModels()
    {
        // register all the system models
        $this->registerModelDir(APP_DIR . DS . 'models');
    }

    /**
     * register a directory of models
     *
     * @param   string  path to the directory to register
     * @return  void
     */
    public function registerModelDir($path)
    {
        $directory = new \DirectoryIterator($path);

        foreach ($directory as $file) {
            if ($file->isDot()) {
                continue;
            }

            $class_name = Str::studly($file->getBasename('.php'));

            $this->models[$class_name] = $file->getPathname();
        }
    }
}
