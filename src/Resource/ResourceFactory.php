<?php


namespace App\Resource;


use App\Exception\UriNotFound;

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
//        if (class_exists($className)) {
//            return $className;
//        } else {
//            throw new UriNotFound('Uri Not Found!');
//        }
    }

    static public function create($className)
    {
        if (class_exists($className)) {
            return new $className();
        } else {
            throw new UriNotFound('Uri Not Found!', 100);
        }
    }

    static public function get($resourceType)
    {
        try {
            $className = self::getClassName($resourceType);
            return self::create($className);
        } catch (UriNotFound $e) {
            return $e->getMessage() . ' code: ' . $e->getCode();
        }
    }

}