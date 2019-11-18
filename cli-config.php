<?php

// cli-config.php
use App\Command\DataFixturesCommand;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

require_once "bootstrap.php";

$db = $entityManager->getConnection();
$helperSet = new HelperSet(array(
    'db' => new ConnectionHelper($db),
    'question' => new QuestionHelper()
));

$cli = ConsoleRunner::createApplication($helperSet, [
    // Data Fixtures Commands
    new DataFixturesCommand(),
    // Migrations Commands
    new DiffCommand(),
    new ExecuteCommand(),
    new GenerateCommand(),
    new LatestCommand(),
    new MigrateCommand(),
    new StatusCommand(),
    new VersionCommand()
]);

return $cli->run();