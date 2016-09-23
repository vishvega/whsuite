<?php

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
$log_file = STORAGE_DIR . DS . 'logs' . DS . 'installer-logfile-' . strtoupper(date('dMY')) . '.log';

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
// start the View
// -------------------------------------------------------------------
App::factory('\Core\View')->setThemeDir(APP_DIR . DS . 'themes');

// -------------------------------------------------------------------
// start assets handler
// -------------------------------------------------------------------
App::factory('\Core\Assets');

// -------------------------------------------------------------------
// start router / run
// -------------------------------------------------------------------
App::factory('\Core\Router');

// load install routes
require_once(APP_DIR . DS . 'configs' . DS . 'installer_routes.php');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// get the route based on the path and server
$route = App::get('router')->match($path, $_SERVER);

$db_config = App::get('configs')->get('database.mysql');
use Illuminate\Database\Capsule\Manager as Capsule;

if (
    ! empty($db_config) &&
    $route->name != 'install-start'
) {

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
    App::add('db', $capsule);
    // -------------------------------------------------------------------
    // register models (main and addons)
    // -------------------------------------------------------------------
    App::get('autoloader')->registerModels();

}


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
// run
// -------------------------------------------------------------------

App::get('dispatcher')->run();

App::stop();
