<?php


namespace App\Action;


use App\Router\BaseRouter;
use Psr\Container\ContainerInterface;


class MemberLoginAction
{
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args) {


        $response->getBody()->write("Member Login!");
        return BaseRouter::staticResponse($response, 200);
    }
}