<?php


namespace App\Resource;


use App\Exception\ApiAccessDeniedException;
use App\Exception\ExceptionResponse;

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

    private $apiRolePermission = [
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
        if (!empty($role) && is_null($this->apiRolePermission[$method]))
            $this->apiRolePermission[$method] = [];

        array_push($this->apiRolePermission[$method], $role);
    }

    protected function checkRolePermission($request)
    {
        $jwt = $request->getAttribute("jwt");
        $authRole = $jwt['authRole'] ? : null;
        $method = $request->getMethod() ? : null;

        $acceptedRole = $this->apiRolePermission[$method];

        $pass = is_null($acceptedRole) ? false : $acceptedRole[0] === '*';
        $pass = $pass || $method === "OPTIONS";

        if (!$pass) {
            if (is_array($acceptedRole) && is_array($authRole)) {
                foreach ($authRole as $role) {
                    if (in_array($role, $acceptedRole)) {
                        $pass = true;
                        break;
                    }
                }
            }
        }

        try {
            if (is_null($method) || !$pass) {
                throw new ApiAccessDeniedException();
            }
        } catch (ApiAccessDeniedException $e) {
            ExceptionResponse::response($e->getMessage(), $e->getCode());
        }
    }

}