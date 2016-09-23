<?php
/**
 * Admin Dashboard Controller
 *
 * The dashboard controller handles the admin dashboard display, including the
 * retrieval of information to show in the dashboard panels.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class DashboardController extends AdminController
{
    /**
     * Index
     *
     * This is the dashboard of the admin area.
     */
    public function index($page = 1, $per_page = null)
    {
        //App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        //App::get('breadcrumbs')->build();

        $widgets = $this->admin_user->getDashboardWidgets();
        $shortcuts = $this->admin_user->getDashboardShortcuts();

        $this->view->set(
            array(
                'title' => $this->lang->get('dashboard'),
                'widgets' => $widgets,
                'shortcuts' => $shortcuts
            )
        );

        $this->assets->addScript('dashboard.js');
        $this->view->display('dashboard/index.php');
    }
}
