<?php

use App\Service\MyRouteService;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . './../bootstrap.php';

$app = (new MyRouteService($entityManager))->get();
$app->run();