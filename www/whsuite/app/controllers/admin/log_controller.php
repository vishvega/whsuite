<?php
/**
 * Admin Log Controller
 *
 * The admin log controller handles the display of the system action logs.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class LogController extends AdminController
{

    public function viewLogs($page = 1)
    {
        $title = $this->lang->get('action_logs');
        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('title', $title);

        $logs = Log::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, array(), false, null, 'admin-action-logs-paging');

        $this->view->set('logs', $logs);
        $this->view->set('total_logs', count(Log::count()));

        $this->view->display('logs/viewLogs.php');
    }

}
