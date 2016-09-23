<?php

namespace Core;

use App;

class CustomException extends \Exception
{
    /**
     * overwrite constructor allowing us to log thrown exceptions
     *
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);

        // Log
        App::get('logger')->addRecord($code, $message, $backtrace);

        parent::__construct($message, $code, $previous);
    }
}
