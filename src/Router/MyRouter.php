<?php

namespace App\Router;

use App\Entity\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use Swift_Message;

class MyRouter extends BaseRouter
{
    public function __construct($entityManager)
    {
        parent::__construct($entityManager);

        // Route 設定
        $this->setRoute($entityManager);

        // Error Handling
        $this->setError();
    }

    /**
     * Route 設定
     * @param $entityManager
     */
    public function setRoute($entityManager)
    {
        $self = $this;

        $this->app->get('/', function (Request $request, Response $response, $args) use ($self) {
            // get monolog
            $logger = $this->get('logger');
            $logger->warning('Foo');
            $logger->error('Bar');

            $employeeService = $this->get('employeeService');
            $response->getBody()->write("Hello world! " . $employeeService->showEmployee('ben'));
            return $self->response($response);
        });

        $this->app->get('/mail', function (Request $request, Response $response, $args) use ($self) {
            // get swift mailer
            $mailer = $this->get('mailer');

            try {
                // Create a message
                $message = (new Swift_Message('Wonderful Subject'))
                    ->setFrom(['ben@jesda.com.tw' => 'ben'])
                    ->setTo(['receiver@domain.org', 'other@domain.org' => 'A name'])
                    ->setBody('Here is the message itself')
                ;

                // Send the message
                $mailer->send($message);

                $response->getBody()->write("Mail is Send! ");
                return $self->response($response);
            } catch (\Swift_RfcComplianceException $e) {
                echo "<pre>";
                print_r($e);
                echo "</pre>";
                die();
            }
        });

        $this->app->get('/hello/{name}[/age/{age}]', function (Request $request, Response $response, $args) use ($self) {
            $name = $args['name'];
            $age = isset($args['age']) ? $args['age'] : '?';
            $response->getBody()->write("Hello, $name, $age");
            return $self->response($response);
        });

        $this->app->get('/create', function (Request $request, Response $response, $args) use ($self, $entityManager) {
            $product = new Product();
            $product->setName('ben');
            $entityManager->persist($product);
            $entityManager->flush();

            $response->getBody()->write("Create! ID: " . $product->getId());
            return $self->response($response);
        });

        $this->app->get('/read', function (Request $request, Response $response, $args) use ($self, $entityManager) {
            $productRepository = $entityManager->getRepository(Product::class);
            $products = $productRepository->getById(3);
            $product = $products[0];
            $response->getBody()->write("Read! ID: " . $product->getName());
            return $self->response($response);
        });

        $this->app->get('/json', function (Request $request, Response $response, $args) use ($self, $entityManager) {
            $data = array('name' => 'Rob', 'age' => 40);
            $payload = json_encode($data);

            $response->getBody()->write($payload);
            return $self->response($response);
        });

        $this->app->group('/users/{id:[0-9]+}', function (RouteCollectorProxy $group) use ($self) {
            $group->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, $response, $args) use ($self) {
                // Find, delete, patch or replace user identified by $args['id']
            })->setName('user');

            $group->get('/reset-password', function ($request, $response, $args) use ($self) {
                // Route for /users/{id:[0-9]+}/reset-password
                // Reset the password for user identified by $args['id']
                $response->getBody()->write("Hi! User ID:" . $args['id']);
                return $self->response($response);
            })->setName('user-password-reset');
        });
    }

}