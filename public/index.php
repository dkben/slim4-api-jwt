<?php

use Service\MyRoute;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

$app = (new MyRoute($entityManager))->get();
$app->run();