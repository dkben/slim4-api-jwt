<?php
//declare(strict_types=1)

namespace App\Exception;

use Exception;

final class ApiAccessDeniedException extends Exception
{
    public function __construct($message = 'Api Access Denied!', $code = 406, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
