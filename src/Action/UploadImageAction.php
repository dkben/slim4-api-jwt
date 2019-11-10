<?php


namespace App\Action;


use App\Helper\UploadImageHelper;
use App\Router\BaseRouter;
use Psr\Container\ContainerInterface;

class UploadImageAction
{
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args) {
        $message = (new UploadImageHelper('public'))->upload();
        $response->getBody()->write("Upload Image: " . $message . "!");
        return BaseRouter::staticResponse($response);
    }
}