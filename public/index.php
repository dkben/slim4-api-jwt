<?php


use App\Helper\SlackHelper;
use App\Router\MyRouter;
use Symfony\Component\Yaml\Yaml;


require __DIR__ . '/../vendor/autoload.php';
$systemConfig = Yaml::parseFile(__DIR__ . '/../config/system.yaml');
require __DIR__ . './../bootstrap.php';


error_reporting(E_ALL);
ini_set('display_errors', $systemConfig['php']['displayErrors']);
ini_set('log_errors', $systemConfig['php']['logErrors']);
ini_set('error_log', __DIR__ . '/../data/php-errors.log');


set_error_handler(function ($error_no, $error_str, $error_file, $error_line) {
    SlackHelper::send("ErrorNo: " . $error_no . " ErrorStr: " . $error_str);
}, E_ALL | E_STRICT);


try {
    $dotenv = Dotenv\Dotenv::create(__DIR__ . '/../');
    $dotenv->load();

    // 自訂的 session 位置
    if (!file_exists(__DIR__ . $systemConfig['session']['path'])) {
        mkdir(__DIR__ . $systemConfig['session']['path'], 0755 ,true);
    }
    session_save_path(__DIR__ . $systemConfig['session']['path']);

    // Start the session
    session_start();

    $app = (new MyRouter())->get();
    $app->run();
} catch (\Exception $error) {
    SlackHelper::send($error->getMessage());
    echo var_export($error, true) . PHP_EOL;
} catch (\TypeError $error) {
    SlackHelper::send($error->getMessage());
    echo var_export($error, true) . PHP_EOL;
} catch (\Error $error) {
    SlackHelper::send($error->getMessage());
    echo var_export($error, true) . PHP_EOL;
}