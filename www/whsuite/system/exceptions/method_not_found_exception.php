<?php

namespace Core\Exceptions;

use Core\CustomException;
use Monolog\Logger;
use App;

class MethodNotFoundException extends CustomException
{

    public function __construct($message = null, $code = 503, \Exception $previous = null)
    {
        $str = 'There was an error trying to load the action you requested.<br /><br />';

        if (is_array($message)) {

            $str .= (! empty($message['addon'])) ? '<strong>Addon: </strong>'. $message['addon'].'<br />' : '';
            $str .= (! empty($message['sub-folder'])) ? '<strong>Sub-Folder: </strong>'. $message['sub-folder'].'<br />' : '';
            $str .= (! empty($message['controller'])) ? '<strong>Controller: </strong>'. $message['controller'].'<br />' : '';
            $str .= (! empty($message['action'])) ? '<strong>Action: </strong>'. $message['action'].'<br />' : '';
        }

        parent::__construct($str, Logger::WARNING, $previous);
    }
}
