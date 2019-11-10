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

    static public function checkSelf()
    {
        if (is_null(self::$redis)) {
            self::create();
        }
    }

    static public function save($key, $message)
    {
        self::checkSelf();
        self::$redis->set($key, $message);
    }

    static public function get($key)
    {
        self::checkSelf();
        return self::$redis->get($key);
    }
}