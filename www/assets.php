<?php

if (! defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// include the setup
if (file_exists('inc' . DS . 'inc.php')) {

    require_once('inc' . DS . 'inc.php');
} else {

    die("Fatal Error: System includes file not found!");
}

// double check we're not trying to go into a directory we shouldn't be!
$proceed = true;

if (is_array($_GET)) {

    foreach ($_GET as $key => $value) {

        if (strpos($value, '../') !== false) {

            $proceed = false;
        }
    }

} else {

    $proceed = false;
}

// check if we can proceed
if ($proceed) {

    // build path to the asset
    if (isset($_GET['path'])) {

        // addon asset
        // go straight to addon directory

        $asset_path = ADDON_DIR . DS;

        if (! empty($_GET['addon'])) {

            $asset_path .= $_GET['addon'] . DS;
        }

        $asset_path .= 'assets' . DS;

        if (! empty($_GET['type'])) {

            $asset_path .= $_GET['type'] . DS;
        }

        if (! empty($_GET['asset'])) {

            $asset_path .= $_GET['asset'];
        }

    } else {

        // addon or main asset
        // build up the path, if addon exists, add it in for addon overwrite.

        $asset_path = APP_DIR . DS . 'themes' . DS;

        if (! empty($_GET['theme'])) {

            $asset_path .= $_GET['theme'] . DS;
        }

        if (! empty($_GET['addon'])) {

            $asset_path .= $_GET['addon'] . DS;
        }

        $asset_path .= 'assets' . DS;

        if (! empty($_GET['type'])) {

            $asset_path .= $_GET['type'] . DS;
        }

        if (! empty($_GET['asset'])) {

            $asset_path .= $_GET['asset'];
        }
    }

    // clean up
    $asset_path = htmlentities($asset_path, ENT_QUOTES);

    if (file_exists($asset_path) && is_file($asset_path)) {

        // get the mime type
        if (substr($asset_path, -3) == '.js') {

            $mime_type = 'text/javascript';

        } elseif (substr($asset_path, -4) == '.css') {

            $mime_type = 'text/css';
        } else {

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($asset_path);
        }

        // display
        ob_start();
        header("Content-type: " . $mime_type);

        echo file_get_contents($asset_path);

        ob_end_flush();
        die();
    }
}

// if we're here, something went wrong, most likely the file doesn't exist.
header("Location: /page-not-found");
