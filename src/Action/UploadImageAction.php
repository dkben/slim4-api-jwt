<?php


namespace App\Action;


use App\Resource\UploadImageResource;
use Psr\Container\ContainerInterface;


class UploadImageAction
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $uploadImageResource = new UploadImageResource($request, $response, $args);
        return $uploadImageResource->post();
    }
}