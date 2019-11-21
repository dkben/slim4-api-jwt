<?php
//declare(strict_types=1)

namespace App\Exception;

use Exception;

final class EntityValidateException extends Exception
{
    public function __construct($message = 'Entity Validate Exception!', $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
