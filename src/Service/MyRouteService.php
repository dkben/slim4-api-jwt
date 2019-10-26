<?php

namespace App\Service;

use DI\Container;
use App\Entity\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Throwable;

class MyRouteService
{
    private $app;
    private $beforeMiddleware;
    private $afterMiddleware;
    private $afterMiddleware2;
    private $afterMiddleware3;

    public function __construct($entityManager)
    {
        // 設定 DI Container
        $container = $this->setContainer();

        // 建立 app
        AppFactory::setContainer($container);
        $this->app = AppFactory::create();

        // Middleware - Before
        $this->setBefore();
        $this->setAfter();
        // Middleware - After
        $this->app->add($this->beforeMiddleware);
        $this->app->add($this->afterMiddleware);
        $this->app->add($this->afterMiddleware2);

        // Route 設定
        $this->setRoute($entityManager);

        // Error Handling
        $this->setError();
    }

    /**
     * Middleware - Before
     */
    public function setBefore()
    {
        $this->beforeMiddleware = function (Request $request, RequestHandler $handler) {
            $response = $handler->handle($request);
            $existingContent = (string) $response->getBody();

            $response = new \Slim\Psr7\Response();
            $response->getBody()->write('BEFORE' . $existingContent);

            return $response;
        };
    }

    /**
     * Middleware - After
     */
    public function setAfter()
    {
        // 結束 route 本身工作後的 route
        $this->afterMiddleware = function ($request, $handler) {
            $response = $handler->handle($request);
            $response->getBody()->write('AFTER');
            return $response;
        };

        $this->afterMiddleware2 = function ($request, $handler) {
            $response = $handler->handle($request);
            $response->getBody()->write('AFTER2');
            return $response;
        };

        $this->afterMiddleware3 = function ($request, $handler) {
            $response = $handler->handle($request);
            $response->getBody()->write('AFTER3');
            return $response;
        };
    }

    public function get()
    {
        return $this->app;
    }

    /**
     * 設定 DI Container
     * @return Container
     */
    public function setContainer()
    {
        $container = new Container();
        $container->set('employeeService', function () {
            // $settings = [...]; // 如果有需要，Service 的設定值
            return new EmployeeService();  // 再把設定傳入 Service 中
        });

        return $container;
    }

    /**
     * Route 設定
     * @param $entityManager
     */
    public function setRoute($entityManager)
    {
        $this->app->get('/', function (Request $request, Response $response, $args) {
            $employeeService = $this->get('employeeService');

            $response->getBody()->write("Hello world! " . $employeeService->showEmployee('ben'));
            return $response;
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