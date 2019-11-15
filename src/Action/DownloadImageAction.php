<?php


namespace App\Action;


use App\Resource\DownloadImageResource;
use Psr\Container\ContainerInterface;

class DownloadImageAction
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $downloadImageResource = new DownloadImageResource($request, $response, $args);
        $downloadImageResource->get();
    }
}