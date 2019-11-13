<?php


namespace App\Router;


use App\Exception\AuthErrorException;
use App\Exception\ExceptionResponse;
use App\Middleware\CommonAfter2Middleware;
use App\Middleware\CommonAfterMiddleware;
use App\Middleware\CommonBeforeMiddleware;
use App\Service\EmployeeService;
use DI\Container;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Redis;
use Slim\Factory\AppFactory;
use Swift_Mailer;
use Swift_SmtpTransport;
use Tuupola\Middleware\JwtAuthentication;


class BaseRouter
{
    protected $config;
    public $app;

    public function __construct()
    {
        $this->config = $GLOBALS['systemConfig'];

        // 設定 DI Container
        $container = $this->setContainer();
        // 建立 app
        AppFactory::setContainer($container);
        $this->app = AppFactory::create();

        // Middleware - Before
        $beforeMiddleware = (new CommonBeforeMiddleware())->run();
        $this->app->add($beforeMiddleware);
        // Middleware - After
        $afterMiddleware = (new CommonAfterMiddleware())->run();
        $this->app->add($afterMiddleware);
        $after2Middleware = (new CommonAfter2Middleware())->run();
        $this->app->add($after2Middleware);

        // Jwt 認證
        $this->app->add(new JwtAuthentication([
            "relaxed" => ["localhost", "127.0.0.1"],
//            "header" => "X-Token",
//            "regexp" => "/(.*)/",
            "attribute" => "jwt",
            "secret" => getenv('JWT_SECRET'),
            "algorithm" => ["HS256"],
            "path" => ["/api/v1/member", "/api/v1/workbench"],  // 受保護區域
            "error" => function () {  // 失敗時處理
                try {
                    throw new AuthErrorException();
                } catch (AuthErrorException $e) {
                    ExceptionResponse::response($e->getMessage(), $e->getCode());
                }
            }
        ]));
    }

    public function get()
    {
        return $this->app;
    }

    /**
     * 設定 DI Container
     * @return Container
     */
    private function setContainer()
    {
        $container = new Container();

        $container->set('entityManager', function () {
            GLOBAL $entityManager;
            return $entityManager;
        });

        // Custom Service
        $container->set('employeeService', function () {
            // $settings = [...]; // 如果有需要，Service 的設定值
            return new EmployeeService();  // 再把設定傳入 Service 中
        });

        // MonoLog
        $container->set('logger', function () {
            $this->checkLogSize();
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

        // Redis
        $container->set('redis', function () {
            $redis = new Redis();
            $redis->connect($this->config['redis']['host'], $this->config['redis']['port']);
            return $redis;
        });

//        $container->set('errorHandler', function ($container) {
//            return function ($request, $response, $exception) use ($container) {
//                return $response->withStatus(500)
//                    ->withHeader('Content-Type', 'text/html')
//                    ->write('Something went wrong!');
//            };
//        });

        return $container;
    }

    public function response($response, $status = 200, $type = 'Content-Type', $header = 'application/json')
    {
        return $response
            ->withHeader($type, $header)
            ->withStatus($status)
            ;
    }

    static public function staticResponse($response, $status = 200, $type = 'Content-Type', $header = 'application/json')
    {
        return $response
            ->withHeader($type, $header)
            ->withStatus($status)
            ;
    }
}