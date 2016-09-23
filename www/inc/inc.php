<?php

// ----------------------------------------------------------------------
// WHSUITE Folder name
// ----------------------------------------------------------------------

$whs_dir_name = 'whsuite';

// ----------------------------------------------------------------------
// WHSUITE Folder Location - relative to the index.php / asset.php files
// ----------------------------------------------------------------------

$whs_dir = '';

// ----------------------------------------------------------------------
// Development Mode - Used to turn on error reporting
// (shoud not be used in a production environment!)
// ----------------------------------------------------------------------

define('DEV_MODE', false);

// ----------------------------------------------------------------------
// DON'T EDIT ANYTHING BELOW THIS LINE UNLESS YOU KNOW WHAT YOU'RE DOING
// ----------------------------------------------------------------------

// Define some constants
define('DOC_ROOT', rtrim(realpath($_SERVER['DOCUMENT_ROOT']), '/'));

define('PUBLIC_DIR', dirname(__DIR__));

// work out if WHSuite is in a Sub directory (i.e. mysite.com/billing) so we can amend URLS as required
list($prefix, $url_prefix) = explode(DOC_ROOT, PUBLIC_DIR);
if (! empty($url_prefix) && substr($url_prefix, 0, 1) != '/') {
    $url_prefix = '/' . $url_prefix;
}

define('URL_PREFIX', $url_prefix);

define('ROOT_DIR', PUBLIC_DIR . DS . $whs_dir);

define('SYS_DIR', PUBLIC_DIR . DS . $whs_dir . $whs_dir_name . DS . 'system');

define('APP_DIR', PUBLIC_DIR . DS . $whs_dir . $whs_dir_name . DS . 'app');

define('ADDON_DIR', PUBLIC_DIR . DS . $whs_dir . $whs_dir_name . DS . 'app' . DS . 'addons');

define('STORAGE_DIR', PUBLIC_DIR . DS . $whs_dir . $whs_dir_name . DS . 'app' . DS . 'storage');

define('VENDOR_DIR', PUBLIC_DIR . DS . $whs_dir . $whs_dir_name . DS . 'vendor');

define('INSTALLER_DIR', APP_DIR . DS . 'installer');
