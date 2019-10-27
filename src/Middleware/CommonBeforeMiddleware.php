<?php


namespace App\Middleware;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

/**
 * Middleware - Common Before
 * Class CommonBeforeMiddleware
 * @package App\Middleware
 */
class CommonBeforeMiddleware
{
    private $beforeMiddleware;

    public function __construct($self)
    {
        $this->beforeMiddleware = function (Request $request, RequestHandler $handler) use ($self) {
            $response = $handler->handle($request);
            $existingContent = (string) $response->getBody();

            $response = new Response();
            $response->getBody()->write('BEFORE->' . $existingContent);
            return $self->response($response);
        };
    }

    public function run()
    {
        return $this->beforeMiddleware;
    }
}