<?php

namespace Core\Exceptions;

use Core\CustomException;
use Monolog\Logger;
use App;

class AssetNotFoundException extends CustomException
{

    public function __construct($message = null, $code = 404, \Exception $previous = null)
    {
        parent::__construct(
            'There was an error trying to find an asset: ' . $message,
            Logger::WARNING,
            $previous
        );
    }
}
