<?php


namespace App\Action;


use App\Router\BaseRouter;
use Psr\Container\ContainerInterface;

class HomeAction
{
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args) {
        $redis = $this->container->get('redis');
        $redis->set('slim4', 'hi, ben');

        $response->getBody()->write("Home Action, Hello world!");
        return BaseRouter::staticResponse($response);
    }
}