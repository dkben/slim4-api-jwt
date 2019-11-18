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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

require_once "bootstrap.php";

$paths = [__DIR__ . '/src/Entity'];
$isDevMode = true;

$dbParams = include 'migrations-db.php';

$entityManager = EntityManager::create($dbParams, $config);
$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

$config = Setup::createAnnotationMetadataConfiguration(
    $paths,
    $isDevMode,
    null,
    null,
    false);

$helperSet = new HelperSet(array(
    'em' => new EntityManagerHelper($entityManager),
    'db' => new ConnectionHelper($entityManager->getConnection()),
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