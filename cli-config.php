<?php
/**
 * cli-config.php
 * 這支檔案 for Doctrine CLI 指令使用，部份設定會與 bootstrap.php 重覆
 */

use App\Command\DataFixturesCommand;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Yaml\Yaml;

$systemConfig = Yaml::parseFile(__DIR__ . '/config/system.yaml');

$dbParams = include_once __DIR__ . '/config/db-config.php';

$connection = DriverManager::getConnection($dbParams);
$configuration = new Configuration($connection);
$configuration->setName('App Migrations');
$configuration->setMigrationsNamespace('App\Migrations');
$configuration->setMigrationsTableName('doctrine_migration_versions');
$configuration->setMigrationsColumnName('version');
$configuration->setMigrationsColumnLength(255);
$configuration->setMigrationsExecutedAtColumnName('executed_at');
$configuration->setMigrationsDirectory(__DIR__ . '/src/Migrations');
$configuration->setAllOrNothing(true);
$configuration->setCheckDatabasePlatform(false);

AnnotationRegistry::registerUniqueLoader('class_exists');

$paths = [__DIR__ . '/src/Entity'];
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
    $paths,
    $isDevMode,
    null,
    null,
    false);

$entityManager = EntityManager::create($dbParams, $config);
$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

//$helperSet = new HelperSet();
//$helperSet->set(new QuestionHelper(), 'question');
//$helperSet->set(new EntityManagerHelper($entityManager), 'em');
//$helperSet->set(new ConnectionHelper($connection), 'db');
//$helperSet->set(new ConfigurationHelper($connection, $configuration));
// or 以下也可以
$helperSet = new HelperSet(array(
    'em' => new EntityManagerHelper($entityManager),
    'db' => new ConnectionHelper($entityManager->getConnection()),
    'question' => new QuestionHelper(),
    new ConfigurationHelper($connection, $configuration)
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