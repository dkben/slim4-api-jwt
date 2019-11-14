<?php


namespace App\Resource;


abstract class BaseResource
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager = null;

    protected $app = null;

    protected $request, $response, $args;

    public function __construct($request, $response, $args)
    {
        GLOBAL $app;
        GLOBAL $entityManager;

        $this->app = $app;
        $this->entityManager = $entityManager;

        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
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

    protected function checkRole($request)
    {
        // 取得 $jwt['role'] 比對這支 Resource 的允許 Method 權限
        $jwt = $request->getAttribute("jwt");
        $role = $jwt['role'];
        $method = $request->getMethod();

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