<?php

use App\Router\MyRouter;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . './../bootstrap.php';

$app = (new MyRouter($entityManager))->get();
$app->run();