<?php

use App\Service\MyRoute;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . './../bootstrap.php';

$app = (new MyRoute($entityManager))->get();
$app->run();