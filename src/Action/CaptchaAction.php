<?php


namespace App\Action;


use App\Router\BaseRouter;
use Gregwar\Captcha\CaptchaBuilder;
use Psr\Container\ContainerInterface;

class CaptchaAction
{
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args) {
        $builder = new CaptchaBuilder;
        $builder->build();
        $_SESSION['phrase'] = $builder->getPhrase();
        header('Content-type: image/jpeg');
        $builder->output();
        return BaseRouter::staticResponse($response, $status = 200, $type = 'Content-Type', $header = 'image/jpeg');
    }
}