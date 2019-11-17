<?php

namespace App\Router;

use App\Action\CaptchaAction;
use App\Action\DataFixturesAction;
use App\Action\DownloadImageAction;
use App\Action\HomeAction;
use App\Action\MemberLoginAction;
use App\Action\ResourceAction;
use App\Action\TestAction;
use App\Action\UploadImageAction;
use App\Action\AdminLoginAction;
use App\Middleware\CommonErrorMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;


class MyRouter extends BaseRouter
{
    private $prefix = '';

    public function __construct()
    {
        parent::__construct();

        $this->prefix = '/api/v1';

        // Route 設定
        $this->setRoute();

        // Error Handling
        (new CommonErrorMiddleware($this))->run();
    }

    /**
     * Route 設定
     * 請注意！Route 規則的擺放順序很重要
     * 固定網址路由放前面，後面才是放動態路由，不然會解析錯誤
     */
    public function setRoute()
    {
        $self = $this;

        // 固定的 uri 用來處理系統排程，非對應到 entity 的狀況
        $this->app->get($this->prefix . '/', function (Request $request, Response $response, $args) use ($self) {
            // 在這裡使用 env 的方式
//            $jwt_secret = getenv('JWT_SECRET');
//            echo $jwt_secret; die;
            $response->getBody()->write("Hello world!");
            return $self->response($response);
        });

        // 單一固定的 uri 可以寫成 Action，直接執行該 Action
        $this->app->get($this->prefix . '/home', HomeAction::class);

        // 固定的 uri 用來處理系統排程，非對應到 entity 的狀況
        $this->app->get($this->prefix . '/test', TestAction::class);

        // 圖片驗證碼
        $this->app->get($this->prefix . '/captcha', CaptchaAction::class);

        // 會員登入認證
        $this->app->post($this->prefix . '/member-login', MemberLoginAction::class);

        // 管理員登入驗證
        $this->app->post($this->prefix . '/admin-login', AdminLoginAction::class);

        // 完全開放 get 的 GET 要獨立寫，可以使用快取，for 前台用戶
        $this->app->get($this->prefix . '/{resourceType}[/id/{id}]', ResourceAction::class . ':get');

        // 需登入 member, admin 身份，不管是那一種 Method 都要經過身份認證，且不能使用快取
        $this->app->group($this->prefix . '/cover', function (RouteCollectorProxy $group) use ($self) {
            // 使用 header 方式下載 private 圖片
            $group->get('/download-image', DownloadImageAction::class);
            // 上傳檔案
            $group->post('/upload-image', UploadImageAction::class);
            // 動態判斷
            $group->get('/{resourceType}[/id/{id}]', ResourceAction::class . ':get');
            $group->post('/{resourceType}', ResourceAction::class . ':post');
            $group->put('/{resourceType}/id/{id}', ResourceAction::class . ':put');
            $group->patch('/{resourceType}/id/{id}', ResourceAction::class . ':patch');
            $group->delete('/{resourceType}/id/{id}', ResourceAction::class . ':delete');
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