<?php

namespace App\Libraries;

abstract class AddonDetails
{
    /**
     * addon details
     */
    protected static $details = array();

    protected static $composer = false;


    /**
     * constructor for created objects
     * setup addon helper for uninstall check
     */
    public function __construct()
    {
        $this->addon_helper = \App::factory('\App\Libraries\AddonHelper');
    }

    /**
     * get the addon details
     *
     * @param   string|null     Optional: null for whole array or specific key
     * @return  string|array
     */
    public static function getDetails($key = null)
    {
        if (! is_null($key) && ! empty(static::$details[$key])) {
            return static::$details[$key];
        }

        return static::$details;
    }

    /**
     * get the addon details
     *
     * @param   int $addon_id   The addons ID within WHSuite database
     * @return  bool
     */
    public function uninstallCheck($addon_id)
    {
        return true;
    }

    /**
     * plugin bootstrap
     * Any code needed to bootstrap the plugin
     *
     * @param string $addonName Pass the addon name in to save working it out again
     * @return bool
     */
    public static function bootstrap($addonName)
    {
        // check for addon vendor
        if (file_exists(ADDON_DIR . DS . $addonName . DS . 'vendor' .DS . 'autoload.php')) {
            include(ADDON_DIR . DS . $addonName . DS . 'vendor' .DS . 'autoload.php');
        }

        // load configs (if any exist)
        if (file_exists(ADDON_DIR . DS . $addonName . DS . 'configs')) {
            if (\App::check('configs')) {
                \App::get('configs')->registerDir(ADDON_DIR . DS . $addonName . DS . 'configs');
            }
        }

        // register any addon models
        if (file_exists(ADDON_DIR . DS . $addonName . DS . 'models')) {
            if (\App::check('autoloader')) {
                \App::get('autoloader')->registerModelDir(ADDON_DIR . DS . $addonName . DS . 'models');
            }
        }

        // check for any hook definitions
        if (file_exists(ADDON_DIR . DS . $addonName . DS . 'configs' .DS . 'hooks.php')) {
            include(ADDON_DIR . DS . $addonName . DS . 'configs' .DS . 'hooks.php');
        }

        return true;
    }
}
