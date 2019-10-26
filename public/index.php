<?php

use App\Router\MyRoute;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . './../bootstrap.php';

$app = (new MyRoute($entityManager))->get();
$app->run();