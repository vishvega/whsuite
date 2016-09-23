<?php
/**
 * Client Announcement  Controller
 *
 * The announcement controller simply shows announcements
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class AnnouncementController extends ClientController
{
    /**
     * Index
     */
    public function index($page = 1, $per_page = null)
    {
        $conditions = array(
            array(
                'type' => 'where',
                'column' => 'is_published',
                'operator' => '=',
                'value' => '1'
            ),
            array(
                'type' => 'where',
                'column' => 'publish_date',
                'operator' => '<=',
                'value' => date('Y-m-d')
            )
        );

        if(!$this->logged_in) {
            $conditions[] = array(
                'type' => 'where',
                'column' => 'clients_only',
                'operator' => '=',
                'value' => '0'
            );
        }

        $title = $this->lang->get('announcements');
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $announcements = Announcement::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'publish_date', 'desc', 'client-announcements-paging');
        $this->view->set('announcements', $announcements);

        return $this->view->display('announcement/index.php');

    }

    public function viewAnnouncement($id)
    {
        $announcement = Announcement::find($id);

        if  (($announcement->is_published == '0' || $announcement->publish_date > time() ||
            (!$this->logged_in && $announcement->clients_only == '1')) ||
            ($this->logged_in && $announcement->individual_language_only == '1' &&
                $this->client->language_id != $announcement->language_id)) {
            // This is a bit of a messy mouthfull but basically it checks to see if:
            // A) The announcement is published
            // B) We're past the publish date
            // C) The user is logged in/has permission to view the announcement
            // D) The announcement is allowed to be visible if the client isnt of the same language
            return header("Location: ".App::get('router')->generate('client-home'));
        }

        $title = $announcement->title;
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'client-home');
        App::get('breadcrumbs')->add($this->lang->get('announcements'), 'client-announcements');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $this->view->set('announcement', $announcement);

        return $this->view->display('announcement/viewAnnouncement.php');
    }
}
