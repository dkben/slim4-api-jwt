<?php


namespace App\Action;


use App\Router\BaseRouter;
use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;


class AdminLoginAction
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $entityManager = $this->container->get('entityManager');
        $data = json_decode($request->getBody()->getContents());
        $admin = $entityManager->getRepository('\App\Entity\Admin')->getByEmail($data->email);

        if (!$admin) {
            $response->getBody()->write("Not find Admin!");
            return BaseRouter::staticResponse($response, 400);
        } else {
            $secret = getenv('JWT_SECRET');
            $jwt = JWT::encode([
                'id' => $admin->getId(),
                'email' => $admin->getEmail(),
//                'exp' => time() + 60,
                'exp' => time() + (60 * 60 * 24),
                'authRole' => ['admin'],
                'scope' => []
            ], $secret, "HS256");

            $response->getBody()->write(json_encode(['jwt' => $jwt]));
            return BaseRouter::staticResponse($response, 200);
        }
    }
    
}