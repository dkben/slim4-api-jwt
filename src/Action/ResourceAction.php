<?php


namespace App\Action;


use App\Resource\ResourceFactory;
use App\Router\BaseRouter;

/**
 * 依照網址動態使用 \Resource\ 裡的 Class Method
 * Class ResourceAction
 * @package App\Action
 */
class ResourceAction extends BaseAction
{
    private function create($request, $response, $args)
    {
        return ResourceFactory::get($request, $response, $args);
    }

    public function get($request, $response, $args)
    {
        $id = isset($args['id']) ? $args['id'] : null;
        $resource = $this->create($request, $response, $args);
        if (is_string($resource)) {
            $data = $resource;
            $status = 400;
        } else {
            $data = $resource->get($id);
            $status = 200;
        }
        $response->getBody()->write($data);
        return BaseRouter::staticResponse($response, $status);
    }

    public function post($request, $response, $args)
    {
        $data = json_decode($request->getBody()->getContents());
        $resource = $this->create($request, $response, $args);
        $response->getBody()->write($resource->post($data));
        return BaseRouter::staticResponse($response, 201);
    }

    public function put($request, $response, $args)
    {
        $id = isset($args['id']) ? $args['id'] : null;
        $data = json_decode($request->getBody()->getContents());
        $resource = $this->create($request, $response, $args);
        $response->getBody()->write($resource->put($id, $data));
        return BaseRouter::staticResponse($response, 200);
    }

    public function patch($request, $response, $args)
    {
        $id = isset($args['id']) ? $args['id'] : null;
        $data = json_decode($request->getBody()->getContents());
        $resource = $this->create($request, $response, $args);
        $response->getBody()->write($resource->patch($id, $data));
        return BaseRouter::staticResponse($response, 200);
    }

    public function delete($request, $response, $args)
    {
        $id = isset($args['id']) ? $args['id'] : null;
        $data = json_decode($request->getBody()->getContents());
        $resource = $this->create($request, $response, $args);
        $response->getBody()->write($resource->delete($id, $data));
        return BaseRouter::staticResponse($response, 200);
    }
}