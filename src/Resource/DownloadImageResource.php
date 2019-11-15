<?php


namespace App\Resource;


use App\Exception\ExceptionResponse;
use App\Exception\FileNotExistsException;
use App\Router\BaseRouter;


class DownloadImageResource extends BaseResource
{
    public function __construct($request, $response, $args)
    {
        parent::__construct($request, $response, $args);

//        $this->appendAuth("GET", '*');
        $this->appendAuth("GET", 'admin');
        $this->appendAuth("GET", 'member');
//        $this->appendAuth("POST", 'admin');
//        $this->appendAuth("PUT", 'admin');
//        $this->appendAuth("PATCH", 'admin');
//        $this->appendAuth("DELETE", 'admin');

        $this->checkRolePermission($request);
    }

    public function get()
    {
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

        return BaseRouter::staticResponse($this->response, $status = 200, $type = 'Content-Type', $header = 'application/octet-stream');
    }

}