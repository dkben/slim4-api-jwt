<?php
//declare(strict_types=1)

namespace App\Exception;

use Exception;

final class AuthErrorException extends Exception
{
    public function __construct($message = 'Auth Error!', $code = 401, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
