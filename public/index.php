<?php

use App\Router\MyRouter;
use Symfony\Component\Yaml\Yaml;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . './../bootstrap.php';

$systemConfig = Yaml::parseFile('../config/system.yaml');
$app = (new MyRouter())->get();
$app->run();