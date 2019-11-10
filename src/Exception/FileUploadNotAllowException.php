<?php


namespace App\Exception;

use App\Helper\SaveLogHelper;
use Exception;

final class FileUploadNotAllowException extends Exception
{
    public function __construct($message = 'File upload is not allowed!', $code = 409, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        SaveLogHelper::save(null, $message);
    }
}
