<?php

namespace App\Router;

use App\Action\HomeAction;
use App\Action\TestAction;
use App\Exception\ExceptionResponse;
use App\Exception\FileNotExistsException;
use App\Exception\TestException;
use App\Helper\UploadImageHelper;
use App\Middleware\CommonErrorMiddleware;
use App\Resource\ResourceFactory;
use Gregwar\Captcha\CaptchaBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

class MyRouter extends BaseRouter
{
    public function __construct()
    {
        parent::__construct();

        // Route 設定
        $this->setRoute();

        // Error Handling，動態 URI 比對 Resource 時，永遠也不會使用到這個 ErrorMiddleware
        (new CommonErrorMiddleware($this))->run();
    }

    /**
     * Route 設定
     */
    public function setRoute()
    {
        $self = $this;

        // 固定的 uri 用來處理系統排程，非對應到 entity 的狀況
        $this->app->get('/', function (Request $request, Response $response, $args) use ($self) {
            $response->getBody()->write("Hello world!");
            return $self->response($response);
        });

        // 單一固定的 URI 可以寫成 Action，直接執行該 Action
        $this->app->get('/home', HomeAction::class);

        // 固定的 uri 用來處理系統排程，非對應到 entity 的狀況
        $this->app->get('/test', TestAction::class);

        // 圖片驗證碼
        $this->app->get('/captcha', function (Request $request, Response $response, $args) use ($self) {
            $builder = new CaptchaBuilder;
            $builder->build();
            $builder->output();
            $_SESSION['phrase'] = $builder->getPhrase();
            return $self->response($response, $status = 200, $type = 'Content-Type', $header = 'image/jpeg');
        });

        $this->app->get('/download', function (Request $request, Response $response, $args) use ($self) {
            // TODO 一些權限檢查...

            $path = '../private/upload/images/2019/11/09/o/';
            $fileName = '6f294fac30f0f2ab2.jpg';

            try {
                if (file_exists($path . $fileName)) {
                    header('Content-Disposition: attachment; filename="' . $fileName . '"');
                    echo file_get_contents($path . $fileName);
                } else {
                    throw new FileNotExistsException($fileName . ' file is not exists!');
                }
            } catch (FileNotExistsException $e) {
                ExceptionResponse::response($e->getMessage(), $e->getCode());
            }

            return $self->response($response, $status = 200, $type = 'Content-Type', $header = 'application/octet-stream');
        });
        
        // 上傳檔案
        $this->app->post('/uploadFile', function (Request $request, Response $response, $args) use ($self) {
            $message = (new UploadImageHelper('private'))->upload();
            $response->getBody()->write("Upload " . $message . "!");
            return $self->response($response);
        });

        // 非固定的 uri 會自動對應到 resource 並使用 entity 對應資料庫
        $this->app->group('', function (RouteCollectorProxy $group) use ($self) {
            $group->get('/{resourceType}[/id/{id}]', function (Request $request, Response $response, $args) use ($self) {
                // 在這裡使用 Redis 的方法
//                $redis = $this->get('redis');
//                $redis->set('slim4', time());

                $id = isset($args['id']) ? $args['id'] : null;
                $resource = ResourceFactory::get($args['resourceType']);
                if (is_string($resource)) {
                    $data = $resource;
                    $status = 400;
                } else {
                    $data = $resource->get($id);
                    $status = 200;
                }
                $response->getBody()->write($data);
                return $self->response($response, $status);
            });

            $group->post('/{resourceType}', function (Request $request, Response $response, $args) use ($self) {
                $data = json_decode($request->getBody()->getContents());
                $resource = ResourceFactory::get($args['resourceType']);
                $response->getBody()->write($resource->post($data));
                return $self->response($response);
            });

            $group->put('/{resourceType}/id/{id}', function (Request $request, Response $response, $args) use ($self) {
                $id = isset($args['id']) ? $args['id'] : null;
                $data = json_decode($request->getBody()->getContents());
                $resource = ResourceFactory::get($args['resourceType']);
                $response->getBody()->write($resource->put($id, $data));
                return $self->response($response);
            });

            $group->patch('/{resourceType}/id/{id}', function (Request $request, Response $response, $args) use ($self) {
                $id = isset($args['id']) ? $args['id'] : null;
                $data = json_decode($request->getBody()->getContents());
                $resource = ResourceFactory::get($args['resourceType']);
                $response->getBody()->write($resource->patch($id, $data));
                return $self->response($response);
            });

            $group->delete('/{resourceType}/id/{id}', function (Request $request, Response $response, $args) use ($self) {
                $id = isset($args['id']) ? $args['id'] : null;
                $data = json_decode($request->getBody()->getContents());
                $resource = ResourceFactory::get($args['resourceType']);
                $response->getBody()->write($resource->delete($id, $data));
                return $self->response($response);
            });
        });

        /*
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
        })->add((new CommonAfter3Middleware())->run());

        $this->app->get('/redis', function (Request $request, Response $response, $args) use ($self) {
            $now = time();
            $redis = $this->get('redis');
            $redis->set('slim4', $now);
            $now = array('now' => $now);
            $payload = json_encode($now);
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
        */
    }
}