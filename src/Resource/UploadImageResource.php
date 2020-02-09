<?php


namespace App\Resource;


use App\Helper\UploadImageHelper;
use App\Router\BaseRouter;


class UploadImageResource extends BaseResource
{
    public function __construct($request, $response, $args)
    {
        parent::__construct($request, $response, $args);

//        $this->appendAuth("GET", '*');
//        $this->appendAuth("GET", 'admin');
//        $this->appendAuth("GET", 'member');
        $this->appendAuth("POST", 'admin');
        $this->appendAuth("POST", 'member');
//        $this->appendAuth("PUT", 'admin');
//        $this->appendAuth("PATCH", 'admin');
//        $this->appendAuth("DELETE", 'admin');

        $this->checkRolePermission($request);
    }

    public function post($data = null)
    {
        $message = (new UploadImageHelper('public'))->upload();
        $this->response->getBody()->write("Upload Image: " . $message . "!");
        return BaseRouter::staticResponse($this->response);
    }
}