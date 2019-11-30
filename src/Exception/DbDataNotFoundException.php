<?php
//declare(strict_types=1)

namespace App\Exception;

use App\Helper\SaveLogHelper;
use Exception;

final class DbDataNotFoundException extends Exception
{
    public function __construct($message = 'Db Data Not Found!', $code = 204, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        SaveLogHelper::save(null, $message);
    }
}
