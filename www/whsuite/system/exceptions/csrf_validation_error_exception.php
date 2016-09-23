<?php

namespace Core\Exceptions;

use Core\CustomException;
use Monolog\Logger;
use App;

class CsrfValidationErrorException extends CustomException
{

    public function __construct($message = null, $code = 400, \Exception $previous = null)
    {
        $str = 'There was an error trying to load the page you requested due to a CSRF Validation Error';

        if (! is_null($message)) {
            $str .= ': '.$message;
        }

        parent::__construct($str, Logger::WARNING, $previous);
    }
}
