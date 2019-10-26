<?php

namespace App\Router;

use App\Entity\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use Swift_Message;
use Throwable;

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
        $this->app->get('/', function (Request $request, Response $response, $args) {
            // get monolog
            $logger = $this->get('logger');
            $logger->warning('Foo');
            $logger->error('Bar');

            $employeeService = $this->get('employeeService');
            $response->getBody()->write("Hello world! " . $employeeService->showEmployee('ben'));
            return $response;
        });

        $this->app->get('/mail', function (Request $request, Response $response, $args) {
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
                return $response;
            } catch (\Swift_RfcComplianceException $e) {
                echo "<pre>";
                print_r($e);
                echo "</pre>";
                die();
            }
        });

        $this->app->get('/hello/{name}[/age/{age}]', function (Request $request, Response $response, $args) {
            $name = $args['name'];
            $age = isset($args['age']) ? $args['age'] : '?';
            $response->getBody()->write("Hello, $name, $age");
            return $response;
        });

        $this->app->get('/create', function (Request $request, Response $response, $args) use ($entityManager) {
            $product = new Product();
            $product->setName('ben');
            $entityManager->persist($product);
            $entityManager->flush();

            $response->getBody()->write("Create! ID: " . $product->getId());
            return $response;
        });

        $this->app->get('/read', function (Request $request, Response $response, $args) use ($entityManager) {
            $productRepository = $entityManager->getRepository(Product::class);
            $products = $productRepository->getById(3);
            $product = $products[0];
            $response->getBody()->write("Read! ID: " . $product->getName());
            return $response;
        })->add($this->afterMiddleware3);

        $this->app->get('/json', function (Request $request, Response $response, $args) use ($entityManager) {
            $data = array('name' => 'Rob', 'age' => 40);
            $payload = json_encode($data);

            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        });

        $this->app->group('/users/{id:[0-9]+}', function (RouteCollectorProxy $group) {
            $group->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, $response, $args) {
                // Find, delete, patch or replace user identified by $args['id']
            })->setName('user');

            $group->get('/reset-password', function ($request, $response, $args) {
                // Route for /users/{id:[0-9]+}/reset-password
                // Reset the password for user identified by $args['id']
                $response->getBody()->write("Hi! User ID:" . $args['id']);
                return $response;
            })->setName('user-password-reset');
        });
    }

    public function setError()
    {
        $app = $this->app;

        // Define Custom Error Handler
        $customErrorHandler = function (
            ServerRequestInterface $request,
            Throwable $exception,
            bool $displayErrorDetails,
            bool $logErrors,
            bool $logErrorDetails
        ) use ($app) {
            $payload = ['error' => $exception->getMessage()];

            $response = $app->getResponseFactory()->createResponse();
            $response->getBody()->write(
                json_encode($payload, JSON_UNESCAPED_UNICODE)
            );

            return $response;
        };

        /*
         *
         * @param bool $displayErrorDetails -> Should be set to false in production
         * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
         * @param bool $logErrorDetails -> Display error details in error log
         * which can be replaced by a callable of your choice.
         *
         * Note: This middleware should be added last. It will not handle any exceptions/errors
         * for middleware added after it.
         */
        $errorMiddleware = $this->app->addErrorMiddleware(true, true, true);
        $errorMiddleware->setDefaultErrorHandler($customErrorHandler);
    }
}