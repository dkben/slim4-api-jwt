<?php

// cli-config.php
use App\Command\DataFixturesCommand;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once "bootstrap.php";

//return ConsoleRunner::createHelperSet($entityManager);

$helperSet = ConsoleRunner::createHelperSet($entityManager);

$cli = ConsoleRunner::createApplication($helperSet, [
    new DataFixturesCommand()
]);

return $cli->run();