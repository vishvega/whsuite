<?php

namespace App\Libraries;

define('DOMPDF_ENABLE_AUTOLOAD', false);
require_once VENDOR_DIR . DS . 'dompdf' . DS . 'dompdf' . DS . 'dompdf_config.inc.php';

class Pdf extends \DOMPDF
{
    function __construct()
    {
        parent::__construct();

        self::set_base_path(\App::get('configs')->get('settings.general.site_url').'/');
    }
}
