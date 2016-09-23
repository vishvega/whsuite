<?php
/**
* Client Base Controller
*
* The client base controller handles any global client functions such as
* enabling authentication. All client and frontend controllers must extend the
* ClientController class.
*
* @package  WHSuite-Controllers
* @author  WHSuite Dev Team <info@whsuite.com>
* @copyright  Copyright (c) 2013, Turn 24 Ltd.
* @license http://whsuite.com/license/ The WHSuite License Agreement
* @link http://whsuite.com
* @since  Version 1.0
*/
class ClientController extends AppController
{
    public $client_auth;
    public $client_user;
    public $client_throttle;
    public $client;
    public $logged_in = false;

    public function onLoad()
    {
        // Set client theme
        App::get('view')->setTheme(App::get('configs')->get('settings.frontend.client_theme'));

        parent::onLoad();

        // Set up client authentication
        App::add('client_auth', \App\Libraries\ClientAuth::auth()); // Load client auth library
        $this->client_auth = &App::get('client_auth'); // Set the auth library into an easier to use variable

        // Set up client login throttling to prevent false logins
        $this->client_throttle = $this->client_auth->getThrottleProvider();
        $this->client_throttle->enable(); // Enable throttling

        // Login auth check
        if ($this->client_auth->check()) {
            $this->client_user = $this->client_auth->getUser();

            // Set individual language if needed
            if (isset($this->client_user->language_id) && $this->client_user->language_id != DEFAULT_LANG) {
                App::get('translation')->init($this->client_user->language_id);
            }

            $this->logged_in = true;
            $this->client = $this->client_user;
            $this->view->set('client', $this->client);

            $client_credit = App\Libraries\Transactions::allClientCredits($this->client->id);
            $client_credit_string = '';
            foreach ($client_credit as $currency_code => $credit) {
                $client_credit_string .= App::get('money')->format($credit, $currency_code).' '.$currency_code.' / ';
            }
            $client_credit_string = rtrim($client_credit_string, ' / ');

            if ($client_credit_string == '') {
                $client_credit_string = $this->lang->get('not_available');
            }
            $this->view->set('client_credit', $client_credit_string);

            $active_service_count = ProductPurchase::where('client_id', '=', $this->client->id)->where('status', '=', '1')->count();
            $this->view->set('active_service_count', $active_service_count);

        } else {
            $this->logged_in = false;
        }

        $this->view->set('logged_in', $this->logged_in);

        if (isset($this->lang->language) && $this->lang->language != '') {
            $this->view->set('language_code', $this->lang->language->slug);
            $this->view->set('text_direction', $this->lang->language->text_direction);
        } elseif ($this->logged_in) {
            $language = Language::find($this->client_user->language_id);
            $this->view->set('language_code', $language->slug);
            $this->view->set('text_direction', $language->text_direction);
        } else {
            $this->view->set('language_code', DEFAULT_LANG);
            $this->view->set('text_direction', 'LTR');
        }

        // Load client menu
        $client_menu = App::factory('\App\Libraries\MenuHelper')->loadMenu('2', true, $this->logged_in);
        $this->view->set('client_menu', $client_menu);
    }
}
