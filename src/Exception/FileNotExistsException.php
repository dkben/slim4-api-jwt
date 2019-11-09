<?php


namespace App\Exception;

use App\Helper\SaveLogHelper;
use Exception;

final class FileNotExistsException extends Exception
{
    public function __construct($message = 'File is Not Exists!', $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        SaveLogHelper::save(null, $message);
    }
}
