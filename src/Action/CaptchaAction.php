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
        $builder->output();
        $_SESSION['phrase'] = $builder->getPhrase();
        return BaseRouter::staticResponse($response, $status = 200, $type = 'Content-Type', $header = 'image/jpeg');
    }
}