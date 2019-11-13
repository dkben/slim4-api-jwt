<?php


namespace App\Resource;


use App\Exception\ExceptionResponse;
use App\Exception\UriNotFoundException;

class ResourceFactory
{
    static protected function getClassName($resourceType)
    {
        // 將 products_test-test 變成 productsTestTest
        $str = preg_replace_callback('/[_|-]([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $resourceType);

        $className = 'App\Resource\\' . ucfirst($str) . 'Resource';
        return $className;
    }

    static public function create($className, $method, $role)
    {
        if (class_exists($className)) {
            return new $className($method, $role);
        } else {
            throw new UriNotFoundException('Uri Not Found!', 100);
        }
    }

    static public function get($args, $method, $role)
    {
        try {
            $className = self::getClassName($args['resourceType']);
            return self::create($className, $method, $role);
        } catch (UriNotFoundException $e) {
            ExceptionResponse::response($e->getMessage(), $e->getCode());
            return '';
        }
    }

}