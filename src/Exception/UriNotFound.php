<?php
//declare(strict_types=1)

namespace App\Exception;

use Exception;

final class UriNotFound extends Exception
{
    public function __construct($message = 'Uri Not Found!', $code = 100, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
