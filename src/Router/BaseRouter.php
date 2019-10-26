<?php


namespace App\Router;


use App\Service\EmployeeService;
use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Swift_Mailer;
use Swift_SmtpTransport;
use Symfony\Component\Yaml\Yaml;

class BaseRouter
{
    protected $config;
    protected $app;
    protected $beforeMiddleware;
    protected $afterMiddleware;
    protected $afterMiddleware2;
    protected $afterMiddleware3;

    public function __construct($entityManager)
    {
        $this->config = Yaml::parseFile('../config/system.yaml');
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

        // Custom Service
        $container->set('employeeService', function () {
            // $settings = [...]; // 如果有需要，Service 的設定值
            return new EmployeeService();  // 再把設定傳入 Service 中
        });

        // MonoLog
        $container->set('logger', function () {
            // create a log channel
            $log = new Logger($this->config['logger']['name']);
            $log->pushHandler(new StreamHandler($this->config['logger']['path'], $this->config['logger']['level']));
            return $log;
        });

        // Swift_Mailer
        $container->set('mailer', function () {
            // Create the Transport
            $transport = (new Swift_SmtpTransport($this->config['mail']['smtp'], $this->config['mail']['port']))
                ->setUsername($this->config['mail']['user'])
                ->setPassword($this->config['mail']['password']);
            // Create the Mailer using your created Transport
            $mailer = new Swift_Mailer($transport);
            return $mailer;
        });

        return $container;
    }

}