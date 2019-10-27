<?php


namespace App\Resource;


use Exception;

class ResourceFactory
{
    static protected function getClassName($resourceType)
    {
        // 將 products_test-test 變成 productsTestTest
        $str = preg_replace_callback('/[_|-]([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $resourceType);

        $className = 'App\Resource\\' . ucfirst($str) . 'Resource';

        if (class_exists($className)) {
            return $className;
        }

        // 沒有符合的 className 時，這裡的 Exception 要自己處理
        throw new Exception('URI not found.');
    }

    static public function get($resourceType)
    {
        $className = self::getClassName($resourceType);
        return new $className();
    }
}