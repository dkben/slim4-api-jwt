<?php


namespace App\Action;


use App\Repository\MemberRepository;
use App\Router\BaseRouter;
use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;


class MemberLoginAction
{
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args) {
        $entityManager = $this->container->get('entityManager');
        $data = json_decode($request->getBody()->getContents());
        $member = $entityManager->getRepository('\App\Entity\Member')->getByEmail($data->email);

        if (!$member) {
            $response->getBody()->write("Not find Member!");
            return BaseRouter::staticResponse($response, 400);
        } else {
            $secret = getenv('JWT_SECRET');
            $jwt = JWT::encode([
                'id' => $member->getId(),
                'email' => $member->getEmail(),
                'exp' => time() + (60 * 60 * 24),
                'scope' => [
                    'Api1' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
                    'Api2' => ['GET'],
                    ]
            ], $secret, "HS256");
            $response->getBody()->write(json_encode(['jwt' => $jwt]));
            return BaseRouter::staticResponse($response, 200);
        }
    }

}