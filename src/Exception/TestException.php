<?php
//declare(strict_types=1)

namespace App\Exception;

use Exception;

final class TestException extends Exception
{
    public function __construct($message = 'Test Exception!', $code = 100, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
