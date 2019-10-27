<?php


namespace App\Resource;



use http\Exception\BadUrlException;

class ResourceFactory
{
    static public function get($resourceType)
    {
        // 將 products_test-test 變成 productsTestTest
        $str = preg_replace_callback('/[_|-]([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $resourceType);
        $className = 'App\Resource\\' . ucfirst($str) . 'Resource';

        if (class_exists($className)) {
            return new $className();
        } else {
            throw new BadUrlException('abc');
        }

    }
}