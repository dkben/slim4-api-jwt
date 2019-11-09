<?php

// 監聽捕獲的錯誤級別
error_reporting(E_ALL);
// 是否開啟錯誤資訊回顯 將錯誤輸出至標準輸出（瀏覽器/命令列）
ini_set('display_errors', true);
// 死否開啟錯誤日誌記錄 將錯誤記錄至 ini：error_log 指定檔案
ini_set('log_errors', true);
ini_set('error_log', __DIR__ . '/../data/php-errors.log');

use App\Router\MyRouter;
use Symfony\Component\Yaml\Yaml;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . './../bootstrap.php';

$systemConfig = Yaml::parseFile('../config/system.yaml');
$app = (new MyRouter())->get();
$app->run();