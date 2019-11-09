<?php


namespace App\Middleware;

use App\Helper\SaveLogHelper;

/**
 * Middleware - Common After
 * Class CommonAfterMiddleware
 * @package App\Middleware
 */
class CommonAfterMiddleware
{
    private $afterMiddleware;

    public function __construct()
    {
        $this->afterMiddleware = function ($request, $handler) {
            SaveLogHelper::save('333', 'ccc');

            $response = $handler->handle($request);
            $response->getBody()->write('->AFTER');
            return $response;
        };
    }

    public function run()
    {
        return $this->afterMiddleware;
    }
}