<?php

namespace Core\Exceptions;

use Core\CustomException;
use Monolog\Logger;
use App;

class ControllerNotFoundException extends CustomException
{

    public function __construct($message = null, $code = 503, \Exception $previous = null)
    {
        $str = 'There was an error trying to load the controller you requested';

        if (! is_null($message)) {
            $str .= ': '.$message;
        }

        parent::__construct($str, Logger::WARNING, $previous);
    }
}
