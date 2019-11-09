<?php


namespace App\Middleware;

use App\Helper\SaveLogHelper;

/**
 * Middleware - Common After
 * Class CommonAfter2Middleware
 * @package App\Middleware
 */
class CommonAfter2Middleware
{
    private $after2Middleware;

    public function __construct()
    {
        $this->after2Middleware = function ($request, $handler) {
            SaveLogHelper::save('444', 'ddd');

            $response = $handler->handle($request);
            $response->getBody()->write('->AFTER2');
            return $response;
        };
    }

    public function run()
    {
        return $this->after2Middleware;
    }
}