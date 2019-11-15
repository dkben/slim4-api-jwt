<?php


namespace App\Action;


use App\Resource\ResourceFactory;
use App\Router\BaseRouter;

/**
 * 依照網址動態使用 \Resource\ 裡的 Class Method
 * 因為這裡的 get, post, put, patch, delete 是獨立的入口點，所以都需要執行 create 方法
 * Class ResourceAction
 * @package App\Action
 */
class ResourceAction extends BaseAction
{
    private $resource = null;

    private function create($request, $response, $args)
    {
        if (is_null($this->resource)) {
            $this->resource = ResourceFactory::get($request, $response, $args);
        }

        return $this->resource;
    }

    /**
     * 獨立的入口點，參數也只能由這裡傳入，所以都需要執行 create 方法
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function get($request, $response, $args)
    {
        $resource = $this->create($request, $response, $args);

        $id = isset($args['id']) ? $args['id'] : null;
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

    /**
     * 獨立的入口點，參數也只能由這裡傳入，所以都需要執行 create 方法
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function post($request, $response, $args)
    {
        $resource = $this->create($request, $response, $args);

        $data = json_decode($request->getBody()->getContents());
        $response->getBody()->write($resource->post($data));
        return BaseRouter::staticResponse($response, 201);
    }

    /**
     * 獨立的入口點，參數也只能由這裡傳入，所以都需要執行 create 方法
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function put($request, $response, $args)
    {
        $resource = $this->create($request, $response, $args);

        $id = isset($args['id']) ? $args['id'] : null;
        $data = json_decode($request->getBody()->getContents());
        $response->getBody()->write($resource->put($id, $data));
        return BaseRouter::staticResponse($response, 200);
    }

    /**
     * 獨立的入口點，參數也只能由這裡傳入，所以都需要執行 create 方法
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function patch($request, $response, $args)
    {
        $resource = $this->create($request, $response, $args);

        $id = isset($args['id']) ? $args['id'] : null;
        $data = json_decode($request->getBody()->getContents());
        $response->getBody()->write($resource->patch($id, $data));
        return BaseRouter::staticResponse($response, 200);
    }

    /**
     * 獨立的入口點，參數也只能由這裡傳入，所以都需要執行 create 方法
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function delete($request, $response, $args)
    {
        $resource = $this->create($request, $response, $args);

        $id = isset($args['id']) ? $args['id'] : null;
        $data = json_decode($request->getBody()->getContents());
        $response->getBody()->write($resource->delete($id, $data));
        return BaseRouter::staticResponse($response, 200);
    }
}