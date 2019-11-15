<?php


namespace App\Resource;


use App\Exception\ExceptionResponse;
use App\Exception\UriNotFoundException;

class ResourceFactory
{
    /**
     * 網址 uri 參數轉換成 class name 並加上命名空間前綴
     * @param $resourceType
     * @return string
     */
    static protected function getClassName($resourceType)
    {
        // 將 products_test-test 變成 productsTestTest
        $str = preg_replace_callback('/[_|-]([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $resourceType);

        $className = 'App\Resource\\' . ucfirst($str) . 'Resource';
        return $className;
    }

    /**
     * 建立動態 Resource 物件
     * @param $request
     * @param $response
     * @param $args
     * @return string
     */
    static public function create($request, $response, $args)
    {
        try {
            $className = self::getClassName($args['resourceType']);

            if (class_exists($className)) {
                return new $className($request, $response, $args);
            } else {
                throw new UriNotFoundException('Uri Not Found!', 100);
            }
        } catch (UriNotFoundException $e) {
            ExceptionResponse::response($e->getMessage(), $e->getCode());
            return '';
        }
    }

}