<?php


namespace App\Exception;


class ExceptionResponse
{
    static public function response($message, $code)
    {
        // 目前 Entity Validate 需要使用 key: value 方式回傳，使用以下方式判別
        if (is_null(json_decode($message))) {
            $response = array('message' => $message, 'code' => $code);
        } else {
            $response = array('message' => json_decode($message), 'code' => $code);
        }

        echo json_encode($response);
        header('Content-Type: application/json');
        http_response_code(400);
        exit;
    }
}