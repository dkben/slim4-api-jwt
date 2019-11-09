<?php


namespace App\Helper;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SaveLogHelper
{
    static public function save($warning = null, $error = null)
    {
        $config = $GLOBALS['systemConfig']['logger'];
        self::checkLogSize($config);

        // create a log channel
        $log = new Logger($config['name']);
        $log->pushHandler(new StreamHandler($config['path'], $config['level']));

        if (!is_null($warning)) {
            $log->warning($warning);
        }

        if (!is_null($error)) {
            $log->error($error);
        }
    }

    /**
     * 限制 log 的檔案數量和大小
     */
    static private function checkLogSize($config)
    {
        // rotate log file on size
        $logName = $config['path'];
        if (file_exists($logName) && filesize($logName) > $config['maxSize']) {
            $path_parts = pathinfo($logName);
            $pattern = $path_parts['dirname']. '/'. $path_parts['filename']. "-%d.". $path_parts['extension'];

            // delete last log
            $fn = sprintf($pattern, $config['maxFiles']);
            if (file_exists($fn))
                unlink($fn);

            // shift file names (add '-%index' before the extension)
            for ($i = $config['maxFiles']-1; $i > 0; $i--) {
                $fn = sprintf($pattern, $i);
                if(file_exists($fn))
                    rename($fn, sprintf($pattern, $i+1));
            }
            rename($logName, sprintf($pattern, 1));
        }
    }

}