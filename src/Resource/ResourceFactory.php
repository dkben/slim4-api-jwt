<?php


namespace App\Resource;


use App\Middleware\CommonErrorMiddleware;
use Exception;

class ResourceFactory
{
    static protected function getClassName($resourceType)
    {
        // 將 products_test-test 變成 productsTestTest
        $str = preg_replace_callback('/[_|-]([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $resourceType);
        return 'App\Resource\\' . ucfirst($str) . 'Resource';
    }

    static public function get($resourceType)
    {
        $className = self::getClassName($resourceType);

        if (class_exists($className)) {
            return new $className();
        }

        // 沒有符合的 className 時，這裡的 Exception 要自己處理
        throw new Exception('URI not found.');
//        if (!class_exists($className)) {
//            (new CommonErrorMiddleware($self))->run();
//            exit;
//        }
    }
}