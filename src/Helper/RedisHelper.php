<?php


namespace App\Helper;


use Redis;

class RedisHelper
{
    static $redis = null;

    static public function create()
    {
        $config = $GLOBALS['systemConfig']['redis'];
        self::$redis = new Redis();
        self::$redis->connect($config['host'], $config['port']);
    }

    static public function save($message)
    {
        if (is_null(self::$redis)) {
            self::create();
        }

        self::$redis->set('slim4', $message);
    }
}