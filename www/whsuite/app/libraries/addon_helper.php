<?php

namespace App\Libraries;

use \Whsuite\Migrations\Migrations;
use \Symfony\Component\Filesystem\Filesystem;
use \Illuminate\Support\Str;

class AddonHelper
{
    public $migrations;
    public $filesystem;

    public function __construct()
    {
        $this->migrations = new Migrations();
        $this->filesystem = new Filesystem();
    }


    /**
     * Install an addon, log in addons table and run the migrations
     *
     * @param   string  The addon directory name
     * @return  bool
     */
    public function install($directory)
    {
        $addon = new \Addon();
        $addon->directory = $directory;
        $addon->is_active = '1';
        $addon->version = $this->getDetails($directory, 'version');

        $addonType = $this->getDetails($directory, 'type');

        if ($addonType == 'server') {
            $addon->is_server = '1';
        } elseif ($addonType == 'gateway') {
            $addon->is_gateway = '1';
        } elseif ($addonType == 'registrar') {
            $addon->is_registrar = '1';
        }

        if (! $addon->save()) {
            return false;
        }

        $this->migrations->migrate($directory, $addon->id);

        \App\Libraries\LanguageHelper::importAddonLanguages($addon);

        \App::get('translation')->purge();

        return true;
    }

    /**
     * Uninstall an addon, reset all the migrations and remove records from database
     *
     * @param   int     The Addon ID we are uninstalling.
     * @return  bool
     */
    public function uninstall($id)
    {
        $addon = \Addon::find($id);

        $allowUninstall = $this->uninstallCheck($addon->directory, $addon->id);

        if (isset($addon->directory) && ! is_null($addon->directory) && $allowUninstall) {
            $this->migrations->reset($addon->directory, $addon->id);

            $addon->delete();

            \App\Libraries\LanguageHelper::uninstallLanguages($addon->id);

            \App::get('translation')->purge();

            return true;
        }

        return false;
    }

    /**
     * run updates on an addon. Currently only updates the version number and migrations
     *
     * @param   int     The Addon ID we are updating.
     * @return  bool
     */
    public function update($id)
    {
        $addon = \Addon::find($id);

        $addon->version = $this->getDetails($addon->directory, 'version');
        $addon->save();

        if (isset($addon->directory) && ! is_null($addon->directory)) {
            $this->migrations->migrate($addon->directory, $addon->id);
        }

        \App::get('translation')->purge();

        return true;
    }

    /**
     * enable an addon, saves uninstalling if it's temporary
     *
     * @param   int     The addon id we are enabling
     * @return  bool
     */
    public function enable($id)
    {
        $addon = \Addon::find($id);

        if (isset($addon->directory) && !is_null($addon->directory)) {
            $addon->is_active = '1';
            $addon->save();

            return true;
        }

        return false;
    }

    /**
     * disable an addon, saves uninstalling if it's temporary
     *
     * @param   int     The addon id we are disabling
     * @return  bool
     */
    public function disable($id)
    {
        $addon = \Addon::find($id);

        if (isset($addon->directory) && ! is_null($addon->directory)) {
            $addon->is_active = '0';
            $addon->save();

            return true;
        }

        return false;
    }

    /**
     * check to see if the addons directory is writable
     *
     * @return bool
     */
    public function addonsDirWritable()
    {
        return is_writable(ADDON_DIR);
    }

    /**
     * get addon details from the addon details class
     *
     * @param   string          The addon directory name
     * @param   string|null     Optional: the key to return from details
     * @return  string|array
     */
    public function getDetails($directory, $key = null)
    {
        $addon_cameled = Str::camel($directory);

        $class_key = strtolower($addon_cameled . 'Details');

        if (\App::check($class_key)) {
            return \App::get($class_key)->getDetails($key);
        } else {
            return \App::factory('\Addon\\' . $addon_cameled . '\\' . $addon_cameled . 'Details')
                ->getDetails($key);
        }
    }

    /**
     * check to see if an addon can be uninstalled
     * allows an addon to stop uninstalling if certain data links exist
     *
     * @param   string          The addon directory name
     * @param   int  $addon_id  Addon Id within WHSuite database
     * @return  bool
     */
    public function uninstallCheck($directory, $addon_id)
    {
        $addon_cameled = Str::camel($directory);

        $class_key = strtolower($addon_cameled . 'Details');

        if (\App::check($class_key)) {
            return \App::get($class_key)->uninstallCheck($addon_id);
        } else {
            return \App::factory('\Addon\\' . $addon_cameled . '\\' . $addon_cameled . 'Details')
                ->uninstallCheck($addon_id);
        }
    }

    /**
     * generic method for checking a domain addon can be uninstalled
     * checks to see if it's been assigned to a domain extension yet.
     *
     * @param   int     $addon_id   Addon ID within WHSuite
     * @return  bool
     */
    public function domainAddonUninstallCheck($addon_id)
    {
        $Registrar = \Registrar::where('addon_id', '=', $addon_id)
            ->with(
                array('Domain', 'DomainExtension')
            )
            ->first();

        $domainCount = (empty($Registrar->Domain) || $Registrar->Domain->count() === 0);
        $extCount = (empty($Registrar->DomainExtension) || $Registrar->DomainExtension->count() === 0);
        if (empty($Registrar) || ($domainCount && $extCount)) {
            return true;
        }

        return false;
    }

    /**
     * generic method for checking a hosting addon can be uninstalled
     * checks to see if it's been assigned to a server group
     *
     * @param   int     $addon_id   Addon ID within WHSuite
     * @return  bool
     */
    public function hostingAddonUninstallCheck($addon_id)
    {
        $ServerModule = \ServerModule::where('addon_id', '=', $addon_id)
            ->with('ServerGroup')
            ->first();

        $ServerGroupCount = (empty($ServerModule->ServerGroup) || $ServerModule->ServerGroup->count() === 0);
        if (empty($ServerModule) || $ServerGroupCount) {
            return true;
        }

        return false;
    }
}
