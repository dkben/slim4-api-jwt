<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;

class HelloWorldController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $view = $this->container->get('view');

        return $view->render($response, 'frontend/HelloWorld.html.twig', [
            'a_variable' => 'test'
        ]);
    }
}