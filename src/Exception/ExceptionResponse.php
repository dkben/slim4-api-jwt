<?php


namespace App\Exception;


class ExceptionResponse
{
    static public function response($message, $code)
    {
        echo "Message: " . $message;
        echo "<br />";
        echo "Code: " . $code;
        header('Content-Type: application/json');
        http_response_code(400);
        exit;
    }
}