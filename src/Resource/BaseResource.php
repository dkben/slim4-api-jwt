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

    protected $app = null;

    public function __construct()
    {
        if ($this->app === null) {
            $this->app = $GLOBALS['app'];
        }
    }

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
            'driver' => $GLOBALS['systemConfig']['db']['driver'],
            'path' => $GLOBALS['systemConfig']['db']['path'],
        );

        // obtaining the entity manager
        $entityManager = EntityManager::create($conn, $config);
        return $entityManager;
    }
}