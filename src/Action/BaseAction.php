<?php


namespace App\Action;


use Psr\Container\ContainerInterface;

abstract class BaseAction
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    abstract protected function get($request, $response, $args);

    abstract protected function post($request, $response, $args);

    abstract protected function put($request, $response, $args);

    abstract protected function patch($request, $response, $args);

    abstract protected function delete($request, $response, $args);

}