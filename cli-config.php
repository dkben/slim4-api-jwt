<?php

// cli-config.php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once "bootstrap.php";

//return ConsoleRunner::createHelperSet($entityManager);

$helperSet = ConsoleRunner::createHelperSet($entityManager);

$cli = ConsoleRunner::createApplication($helperSet, [
//    new \Symfony\Component\Console\Command\Command('app2', 'my'),

]);

return $cli->run();