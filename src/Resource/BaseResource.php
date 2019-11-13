<?php


namespace App\Resource;


abstract class BaseResource
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

    private $APIRolePermission = [
        "GET" => null,
        "POST" => null,
        "PUT" => null,
        "PATCH" => null,
        "DELETE" => null
    ];

    /**
     * @param $method
     * @param $role
     */
    protected function appendAuth($method, $role)
    {
        if (!empty($role) && is_null($this->APIRolePermission[$method]))
            $this->APIRolePermission[$method] = [];

        array_push($this->APIRolePermission[$method], $role);
    }

    protected function checkRole($method, $role)
    {
        echo "<pre>";
        print_r($this->APIRolePermission);
        echo "</pre>";
        echo "<pre>";
        print_r($method);
        echo "</pre>";
        echo "<pre>";
        print_r($role);
        echo "</pre>";
        die();
    }
}