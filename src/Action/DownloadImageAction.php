<?php


namespace App\Action;


use App\Exception\ExceptionResponse;
use App\Exception\FileNotExistsException;
use App\Router\BaseRouter;
use Psr\Container\ContainerInterface;

class DownloadImageAction
{
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args) {
        // TODO 一些權限檢查...

        $path = '../private/upload/images/2019/11/10/o/';
        $fileName = 'd84e275db5a1e7ef.jpg';

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

        return BaseRouter::staticResponse($response, $status = 200, $type = 'Content-Type', $header = 'application/octet-stream');
    }
}