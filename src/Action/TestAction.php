<?php


namespace App\Action;


use App\Exception\ExceptionResponse;
use App\Exception\TestException;
use App\Router\BaseRouter;
use Psr\Container\ContainerInterface;


class TestAction
{
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args) {
        //  隨機成功、失敗
        try {
            if ((bool)random_int(0, 1)) {
                throw new TestException('Hi, Test Exception');
            }
        } catch (TestException $e) {
            ExceptionResponse::response($e->getMessage(), $e->getCode());
        }

        $response->getBody()->write("Test!");
        return BaseRouter::staticResponse($response, 200);
    }
}