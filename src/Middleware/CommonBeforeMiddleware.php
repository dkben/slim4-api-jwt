<?php


namespace App\Middleware;


use App\Helper\SaveLogHelper;
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

    public function __construct()
    {
        $this->beforeMiddleware = function (Request $request, RequestHandler $handler) {
            SaveLogHelper::save('222', 'bbb');

            $response = $handler->handle($request);
            // 有經過 Before 但只處理私下工作，所以不返回值及建立 header
            // 在此建立 header 將會覆蓋後面所有流程的 header 及 status
//            $existingContent = (string) $response->getBody();
//            $response = new Response();
//            $response->getBody()->write('BEFORE->' . $existingContent);
            return $response;
        };
    }

    public function run()
    {
        return $this->beforeMiddleware;
    }
}