<?php


namespace App\Resource;


class BaseResource
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager = null;

    protected $app = null;

    public function __construct()
    {
        GLOBAL $app;
        GLOBAL $entityManager;

        $this->app = $app;
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

}