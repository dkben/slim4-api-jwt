<?php


namespace App\Router;


use App\Service\EmployeeService;
use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Swift_Mailer;
use Swift_SmtpTransport;
use Symfony\Component\Yaml\Yaml;
use Throwable;

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

    public function get()
    {
        return $this->app;
    }

    /**
     * Middleware - Before
     */
    private function setBefore()
    {
        $self = $this;

        $this->beforeMiddleware = function (Request $request, RequestHandler $handler) use ($self) {
            $response = $handler->handle($request);
            $existingContent = (string) $response->getBody();

            $response = new Response();
            $response->getBody()->write('BEFORE' . $existingContent);
            return $self->response($response);
        };
    }

    /**
     * Middleware - After
     */
    private function setAfter()
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

    /**
     * 設定 DI Container
     * @return Container
     */
    private function setContainer()
    {
        $container = new Container();

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

        return $container;
    }

    /**
     * 限制 log 的檔案數量和大小
     */
    private function checkLogSize()
    {
        // rotate log file on size
        $logName = $this->config['logger']['path'];
        if (file_exists($logName) && filesize($logName) > $this->config['logger']['maxSize']) {
            $path_parts = pathinfo($logName);
            $pattern = $path_parts['dirname']. '/'. $path_parts['filename']. "-%d.". $path_parts['extension'];

            // delete last log
            $fn = sprintf($pattern, $this->config['logger']['maxFiles']);
            if (file_exists($fn))
                unlink($fn);

            // shift file names (add '-%index' before the extension)
            for ($i = $this->config['logger']['maxFiles']-1; $i > 0; $i--) {
                $fn = sprintf($pattern, $i);
                if(file_exists($fn))
                    rename($fn, sprintf($pattern, $i+1));
            }
            rename($logName, sprintf($pattern, 1));
        }
    }

    protected function setError()
    {
        $app = $this->app;
        $self = $this;

        // Define Custom Error Handler
        $customErrorHandler = function (
            ServerRequestInterface $request,
            Throwable $exception,
            bool $displayErrorDetails,
            bool $logErrors,
            bool $logErrorDetails
        ) use ($self, $app) {
            $payload = ['error' => $exception->getMessage()];

            $response = $app->getResponseFactory()->createResponse();
            $response->getBody()->write(
                json_encode($payload, JSON_UNESCAPED_UNICODE)
            );

            return $self->response($response);
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

    public function response($response, $status = 201, $type = 'Content-Type', $header = 'application/json')
    {
        return $response
            ->withHeader($type, $header)
            ->withStatus($status);
    }
}