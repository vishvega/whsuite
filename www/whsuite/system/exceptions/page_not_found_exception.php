<?php

namespace Core\Exceptions;

use Core\CustomException;
use Monolog\Logger;
use App;

class PageNotFoundException extends CustomException
{
    public $missingUrl;

    public function __construct($message = null, $code = 404, \Exception $previous = null)
    {
        $this->missingUrl = $message;
        $str = 'There was an error trying to load the page you requested';

        if (! is_null($message)) {
            $str .= ': '.$message;
        }

        parent::__construct($str, Logger::WARNING, $previous);
    }
}
