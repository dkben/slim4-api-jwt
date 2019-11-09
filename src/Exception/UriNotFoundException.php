<?php
//declare(strict_types=1)

namespace App\Exception;

use App\Helper\SaveLogHelper;
use Exception;

final class UriNotFoundException extends Exception
{
    public function __construct($message = 'Uri Not Found!', $code = 100, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        SaveLogHelper::save(null, $message);
    }
}
