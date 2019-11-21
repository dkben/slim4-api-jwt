<?php
/**
 * bootstrap.php
 * 這支檔案 for public/index.php 使用
 */

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;


require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$paths = [__DIR__ . '/src/Entity'];
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
    $paths,
    $isDevMode,
    null,
    null,
    false);

// Use Query Cache - ApcuCache
$config->setQueryCacheImpl(new ApcuCache());
// Use Result Cache - ApcuCache
//$config->setResultCacheImpl(new ApcuCache());

// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

//echo __DIR__ . '/config/db-config.php'; die;
// /Users/ben/Learn/Slim4/first/config/db-config.php
// database configuration parameters
$dbParams = include_once __DIR__ . '/config/db-config.php';

// obtaining the entity manager
$entityManager = EntityManager::create($dbParams, $config);