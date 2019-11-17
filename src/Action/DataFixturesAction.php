<?php


namespace App\Action;


use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use App\Router\BaseRouter;
use Psr\Container\ContainerInterface;


class DataFixturesAction
{
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args) {
        GLOBAL $entityManager;
        $loader = new Loader();
        $loader->loadFromDirectory('../src/DataFixtures');
        $purger = new ORMPurger();
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());

        $response->getBody()->write("Test!");
        return BaseRouter::staticResponse($response, 200);
    }
}