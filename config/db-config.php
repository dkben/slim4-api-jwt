<?php
/**
 * config/db-config.php
 * 資料庫設定檔
 */

return [
    'host' => 'localhost',
    'driver' => $systemConfig['db']['driver'],
    'path' => __DIR__ . $systemConfig['db']['path']
];