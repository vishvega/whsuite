<?php

namespace Core\Exceptions;

use Core\CustomException;
use Monolog\Logger;
use App;

class ControllerNotSpecifiedException extends CustomException
{

    public function __construct($message = null, $code = 503, \Exception $previous = null)
    {
        $str = 'No controller was specified on the requested page.';

        parent::__construct($str, Logger::WARNING, $previous);
    }
}
