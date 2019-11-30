<?php


namespace App\Exception;


class ExceptionResponse
{
    static public function response($message, $code)
    {
        $response = array('message' => $message, 'code' => $code);
        echo json_encode($response);
        header('Content-Type: application/json');
        http_response_code(400);
        exit;
    }
}