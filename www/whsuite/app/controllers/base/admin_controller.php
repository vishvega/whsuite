<?php

/**
* Admin Base Controller
*
* The admin base controller handles any global admin functions such as enabling
* authentication. All admin controllers must extend the AdminController class.
*
* @package  WHSuite-Controllers
* @author  WHSuite Dev Team <info@whsuite.com>
* @copyright  Copyright (c) 2013, Turn 24 Ltd.
* @license http://whsuite.com/license/ The WHSuite License Agreement
* @link http://whsuite.com
* @since  Version 1.0
*/
class AdminController extends AppController
{
    public $admin_auth;
    public $admin_user;
    public $admin_throttle;

    /**
     * template to render
     * used for scaffolding, can set this var if it needs overriding
     * rather then redefining function
     */
    public $render_view = null;

    /**
     * model in use
     * set during scaffolding, this is the model belonging to this controller
     */
    protected $model = null;


    public function onLoad()
    {
        // Set admin theme
        App::get('view')->setTheme(App::get('configs')->get('settings.development.admin_theme'));

        parent::onLoad();

        // Set up admin authentication
        App::add('admin_auth', \App\Libraries\AdminAuth::auth()); // Load admin auth library
        $this->admin_auth = &App::get('admin_auth'); // Set the auth library into an easier to use variable

        // Set up admin login throttling to prevent false logins
        $this->admin_throttle = $this->admin_auth->getThrottleProvider();
        $this->admin_throttle->enable(); // Enable Throttling

        // Run the admin authentication, checking for any methods that need to be ignored (i.e not protected)
        $route = App::get('dispatcher')->getRoute(); // Get the current route from the dispatcher
        $check = $this->admin_auth->check(); // Run the authentication check (true = logged in, false = logged out)

        // Check to see if the current method is on the ignore list
        // if its not, and the user isn't logged in, force them back to the login page.
        if (
            (
                ! empty($this->allow) &&
                ! in_array($route->values['action'], $this->allow) &&
                ! $check
            ) ||
            (
                (
                    ! isset($this->allow) ||
                    empty($this->allow)
                ) &&
                ! $check
            )
        ) {
            return $this->redirect('admin-login');
        }
        // At this point the user has been successfully authenticated.

        // Set an easier to use variable containing the user info.
        $this->admin_user = $this->admin_auth->getUser();
        $this->view->set('user', $this->admin_user);

        // Set individual language if needed
        if ($this->admin_auth->check() && $this->admin_user->language_id != DEFAULT_LANG) {
            App::get('translation')->init($this->admin_user->language_id);
        }

        // Load pending orders
        $orders = Order::where('status', '=', '0')->orderBy('id', 'asc')->get();
        $this->view->set('pending_order_count', $orders->count());
        $this->view->set('pending_orders', $orders);

        // Load admin menu
        $admin_menu = App::factory('\App\Libraries\MenuHelper')->loadMenu('1');
        $this->view->set('admin_menu', $admin_menu);
    }

}
