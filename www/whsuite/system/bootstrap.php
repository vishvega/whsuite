<?php
if (empty($_SERVER['HTTP_AUTHORIZATION']) && ! empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
}

// -------------------------------------------------------------------
// load registry
// -------------------------------------------------------------------
if (file_exists(SYS_DIR . DS . 'app.php')) {
    require_once(SYS_DIR . DS . 'app.php');
} else {
    die("Fatal Error: System app file not found!");
}

// -------------------------------------------------------------------
// load composer
// -------------------------------------------------------------------
if (file_exists(VENDOR_DIR . DS . 'autoload.php')) {
    require_once(VENDOR_DIR . DS . 'autoload.php');
} else {
    die("Fatal Error: Composer autoload file not found!");
}

// -------------------------------------------------------------------
// setup error reporting
// -------------------------------------------------------------------
if (defined('DEV_MODE') && DEV_MODE) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

// -------------------------------------------------------------------
// start the system registry
// register the system files
// -------------------------------------------------------------------
App::start();

// -------------------------------------------------------------------
// start logger so we can log anything that goes wrong
// -------------------------------------------------------------------
$log_file = STORAGE_DIR . DS . 'logs' . DS . 'logfile-' . strtoupper(date('dMY')) . '.log';

App::factory('\Monolog\Logger', 'log')->pushHandler(
    new \Monolog\Handler\StreamHandler($log_file, \Monolog\Logger::WARNING)
);

// -------------------------------------------------------------------
// start session handler
// -------------------------------------------------------------------
App::factory('\Core\Session');

// -------------------------------------------------------------------
// register the configs
// -------------------------------------------------------------------
App::factory('\Core\Configs', APP_DIR . DS . 'configs');

// -------------------------------------------------------------------
// start up hook system
// -------------------------------------------------------------------
App::factory('\Core\Hooks');

// -------------------------------------------------------------------
// load app hooks
// -------------------------------------------------------------------
App::hooks();

// -------------------------------------------------------------------
// load db config / init db/orm
// -------------------------------------------------------------------
$db_config = App::get('configs')->get('database.mysql');

use Illuminate\Database\Capsule\Manager as Capsule;

if (! empty($db_config)) {
    $capsule = new Capsule;
    $capsule->addConnection(array(
        'driver'    => 'mysql',
        'host'      => $db_config['host'],
        'database'  => $db_config['name'],
        'username'  => $db_config['user'],
        'password'  => $db_config['pass'],
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix'    => $db_config['prefix']
    ));

    // Setup the Eloquent ORM...
    $capsule->bootEloquent();
    $capsule->setAsGlobal();

    // -------------------------------------------------------------------
    // register models (main and addons)
    // -------------------------------------------------------------------
    App::get('autoloader')->registerModels();

    // -------------------------------------------------------------------
    // register addons configs / routes (if any exist)
    // -------------------------------------------------------------------
    App::registerAddons();
}

// -------------------------------------------------------------------
// start the View
// -------------------------------------------------------------------
App::factory('\Core\View')->setThemeDir(APP_DIR . DS . 'themes');

// -------------------------------------------------------------------
// start assets handler
// -------------------------------------------------------------------
App::factory('\Core\Assets');

// -------------------------------------------------------------------
// start router
// -------------------------------------------------------------------
App::factory('\Core\Router')->loadRoutes();

// -------------------------------------------------------------------
// run router
// -------------------------------------------------------------------
App::get('hooks')->callListeners('pre_route');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// get the route based on the path and server
$route = App::get('router')->match($path, $_SERVER);

App::get('hooks')->callListeners('post_route', $route);

// -------------------------------------------------------------------
// failed? abort - 404
// -------------------------------------------------------------------
if (! $route || ! is_object($route)) {
    throw new \Core\Exceptions\PageNotFoundException($_SERVER['REQUEST_URI']);
}

// -------------------------------------------------------------------
// success
// check we can actually dispatch to the requested route
// -------------------------------------------------------------------
App::factory('\Core\Dispatcher', $route)->check();

// -------------------------------------------------------------------
// load app bootstrap
// -------------------------------------------------------------------
if (file_exists(APP_DIR . DS . 'bootstrap.php')) {
    require_once(APP_DIR . DS . 'bootstrap.php');
}

// -------------------------------------------------------------------
// run
// -------------------------------------------------------------------
App::get('hooks')->callListeners('pre_dispatch');

App::get('dispatcher')->run();

App::get('hooks')->callListeners('post_dispatch');

App::stop();
