<?php


namespace App\Middleware;

/**
 * Middleware - Common After
 * Class CommonAfter3Middleware
 * @package App\Middleware
 */
class CommonAfter3Middleware
{
    private $after3Middleware;

    public function __construct()
    {
        $this->after3Middleware = function ($request, $handler) {
            $response = $handler->handle($request);
//            $response->getBody()->write('->AFTER3');
            return $response;
        };
    }

    public function run()
    {
        return $this->after3Middleware;
    }
}