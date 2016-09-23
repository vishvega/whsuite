<?php
/**
 * Admin Addon Controller
 *
 * The addon controller allows staff to enable, disable, install, uninstall and
 * manage any system addons.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
use \Illuminate\Support\Str;

class AddonController extends AdminController
{
    public $addon_helper = null;

    public function onLoad()
    {
        parent::onLoad();

        $this->addon_helper = App::factory('\App\Libraries\AddonHelper');
    }

    public function index($page = 1, $per_page = null)
    {
        $title = $this->lang->get('addon_management');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $addons_dir = new \DirectoryIterator(ADDON_DIR);

        $addons = new stdClass;

        foreach ($addons_dir as $dir) {
            if ($dir->isDot() || $dir->isDir() === false) {
                continue;
            }

            $addon_name = $dir->getBasename();
            if (file_exists(ADDON_DIR.'/'.$addon_name.'/'.$addon_name.'_details.php')) {
                $addon_cameled = Str::camel($addon_name);
                $addon_details = App::factory('\Addon\\'.$addon_cameled.'\\'.$addon_cameled.'Details')->getDetails();
            } else {
                continue;
            }

            if (!file_exists(ADDON_DIR.'/'.$addon_name.'/assets/img/logo.png')) {
                $logo_path = 'addon_placeholder_logo.png';
            } else {
                $logo_path = $addon_name.'::logo.png';
            }

            $addon_data = Addon::where('directory', '=', $addon_name)->first();

            if (empty($addon_data) || $addon_data->count() < 1) {
                $addon_data = new stdClass;
            }

            $addons->$addon_name = new stdClass;
            $addons->$addon_name->details = $addon_details;
            $addons->$addon_name->data = $addon_data;
            $addons->$addon_name->logo = $logo_path;
        }

        $this->view->set('addons', $addons);

        $this->view->display('addons/index.php');
    }

    public function manageAddon($id)
    {
        return $this->redirect('admin-addon');
    }

    public function installAddon($slug)
    {
        // register this addons config / model incase we want it during migration up
        $addon_path = ADDON_DIR . DS . $slug . DS;

        if (file_exists($addon_path . 'configs')) {
            App::get('configs')->registerDir($addon_path . 'configs');
        }

        if (file_exists($addon_path . 'models')) {
            App::get('autoloader')->registerModelDir($addon_path . 'models');
        }

        // install
        if ($this->addon_helper->install($slug)) {
            App::get('session')->setFlash('success', $this->lang->get('addon_installed'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_installing_addon'));
        }

        return $this->redirect('admin-addon');
    }

    public function uninstallAddon($id)
    {
        if ($this->addon_helper->uninstall($id)) {
            App::get('session')->setFlash('success', $this->lang->get('addon_uninstalled'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_uninstalling_addon'));
        }

        return $this->redirect('admin-addon');
    }

    public function enableAddon($id)
    {
        if ($this->addon_helper->enable($id)) {
            App::get('session')->setFlash('success', $this->lang->get('addon_enabled'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_enabling_addon'));
        }

        return $this->redirect('admin-addon');
    }

    public function disableAddon($id)
    {
        if ($this->addon_helper->disable($id)) {
            App::get('session')->setFlash('success', $this->lang->get('addon_disabled'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_disabling_addon'));
        }

        return $this->redirect('admin-addon');
    }

    public function updateAddon($id)
    {
        if ($this->addon_helper->update($id)) {
            App::get('session')->setFlash('success', $this->lang->get('addon_updated'));
        } else {
            App::get('session')->setFlash('error', $this->lang->get('error_updating_addon'));
        }

        return $this->redirect('admin-addon');
    }
}
