<?php

namespace Core\Exceptions;

use Core\CustomException;
use Monolog\Logger;
use App;

class TemplateNotFoundException extends CustomException
{

    public function __construct($message = null, $code = 503, \Exception $previous = null)
    {
        parent::__construct(
            'There was an error trying to find a template: ' . $message,
            Logger::CRITICAL,
            $previous
        );
    }
}
