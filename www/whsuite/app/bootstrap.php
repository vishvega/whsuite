<?php
/**
 * Bootstrap
 *
 * The bootstrap file allows us to load in any 3rd party vendor packages, as
 * well as make changes to the system outside of the restrictions of a controller,
 * model or view.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2016, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */

/**
 * Check to see if WHSuite is installed, if not redirect to the install script
 *
 */
function installRedirect()
{
    require_once(APP_DIR . DS . 'configs' . DS . 'installer_routes.php');
    header("Location: " . App::get('router')->generate('install-start'));
    exit;
}

$db_config_path = APP_DIR . DS . 'configs' . DS . 'database.php';
if (file_exists($db_config_path)) {
    $db_config = file_get_contents($db_config_path);
    if (
        ! file_exists(STORAGE_DIR . DS . 'whsuite.installed') ||
        empty($db_config)
    ) {
        installRedirect();
    }
} else {
    installRedirect();
}


/**
 * debug that uses WHSuite utilities debug function
 *
 * @param mixed $var variable to debug
 * @param bool  $die die after displaying?
 */
function debug($var, $die = false)
{
    \Whsuite\Utilities\Utilities::debug($var, $die);
}

/**
 * debug that uses WHSuite utilities pr function
 *
 * @param mixed $var variable to debug
 * @param bool  $die die after displaying?
 */
function pr($var, $die = false)
{
    \Whsuite\Utilities\Utilities::pr($var, $die);
}

// Load the settings handler
$settings = new \Whsuite\Settings\Settings();
$settings->init();

$allSettings = \App::get('configs')->get('settings');

// Load the breadcrumb handler
App::factory('\Whsuite\Breadcrumbs\Breadcrumbs');

// Load the money handler
App::factory('\Whsuite\Money\Money')
    ->init(
        Currency::all(),
        App::get('configs')->get('settings.billing.default_currency')
    );

// Load the mail handler
App::factory('\Whsuite\Email\Email')->load();

// load the translations
App::factory('\Whsuite\Translation\Translation');


// Load default language - NOTE: We need to load all languages anyway, so doing it here to avoid extra queries
$langs = Language::where('is_active', '=', '1')->get();
$languages = array();
foreach ($langs as $lang) {
    $languages[$lang->id] = $lang;
    if ($lang->is_default == '1') {
        define('DEFAULT_LANG', $lang->id); // defining this so we dont have to call to the lang table again.
    }
}

if (! defined('DEFAULT_LANG')) {
    define('DEFAULT_LANG', 1);
}

App::get('configs')->set('languages', $languages); // saves having to load them again!

App::get('translation')->init(DEFAULT_LANG);

// load the forms
\Whsuite\Forms\Forms::init();

// Load the string helper
App::factory('\Illuminate\Support\Str');

// Load the security helper
App::factory('\App\Libraries\Security');

// Load the file helper
App::factory('\Symfony\Component\Filesystem\Filesystem');

// Load the domain helper
App::factory('\App\Libraries\DomainHelper');
