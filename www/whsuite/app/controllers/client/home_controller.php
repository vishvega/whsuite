<?php
/**
 * Client Home Controller
 *
 * The home controller handles the homepage of the client area / frontend of
 * whsuite.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class HomeController extends ClientController
{
    /**
     * Index
     *
     * This is the dashboard of the admin area.
     */
    public function index($page = 1, $per_page = null)
    {
        if ($this->logged_in) {
            App::get('breadcrumbs')->add($this->lang->get('dashboard'));
            App::get('breadcrumbs')->build();

            // Services
            $services = ProductPurchase::where('client_id', '=', $this->client->id)->orderByRaw('status = 0, status ASC')->take(5)->get();
            $this->view->set('services', $services);

            // Invoices
            $invoices = Invoice::where('client_id', '=', $this->client->id)->take(5)->orderBy('id', 'DESC')->get();
            $this->view->set('invoices', $invoices);

            // Announcements
            $client = $this->client;
            $announcements = Announcement::where('language_id', '=', $this->client->language_id)
                ->orWhere(function ($query) use ($client) {
                    $query->where('language_id', '!=', $client->language_id)->where('individual_language_only', '=', '0');
                })
                ->where('publish_date', '<=', time())->where('is_published', '=', '1')
                ->take(5)
                ->get();
            $this->view->set('announcements', $announcements);

            App::get('hooks')->callListeners('client-dashboard-pre-display', $this->client);

            $this->view->display('home/dashboard.php');
        } else {
            App::get('breadcrumbs')->add($this->lang->get('home'));
            App::get('breadcrumbs')->build();

            App::get('hooks')->callListeners('guest-home-pre-display');

            $this->view->display('home/index.php');
        }
    }
}
