<?php


namespace App\Resource;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class BaseResource
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager = null;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if ($this->entityManager === null) {
            $this->entityManager = $this->createEntityManager();
        }

        return $this->entityManager;
    }

    /**
     * @return EntityManager
     */
    public function createEntityManager()
    {
        // Create a simple "default" Doctrine ORM configuration for Annotations
        $isDevMode = true;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src/Entity"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
        // Use Query Cache - ApcuCache
        $config->setQueryCacheImpl(new ApcuCache());
        // Use Result Cache - ApcuCache
        //$config->setResultCacheImpl(new ApcuCache());

        // or if you prefer yaml or XML
        //$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
        //$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

        // database configuration parameters
        $conn = array(
            'driver' => 'pdo_sqlite',
            'path' => '../data/db.sqlite',
        );

        // obtaining the entity manager
        $entityManager = EntityManager::create($conn, $config);
        return $entityManager;
    }
}