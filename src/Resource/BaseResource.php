<?php


namespace App\Resource;


use App\Exception\ApiAccessDeniedException;
use App\Exception\DbDataNotFoundException;
use App\Exception\ExceptionResponse;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    protected function get($id) {}

    protected function post($data = null) {}

    protected function put($id, $data) {}

    protected function patch($id, $data) {}

    protected function delete($id) {}

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

    /**
     * 建立分頁物件
     * @param $queryBuilder
     * @param $limit
     * @param $offset
     * @return Paginator
     */
    protected function createPaginator($queryBuilder, $limit, $offset)
    {
        $paginator = new Paginator($queryBuilder);
        $paginator->getQuery()
            ->setFirstResult($offset) // set the offset
            ->setMaxResults($limit); // set the limit

        return $paginator;
    }

    protected function resourcePost($entityClass, $data)
    {
        $entity = new $entityClass();
        $entity->set($data);
//        $product->setName(isset($data->name) ? $data->name : 'default');
//        $product->setProdDescribe(isset($data->prodDescribe) ? $data->prodDescribe : null);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    protected function resourcePut($entityClass, $id, $data)
    {
        // handle if $id is missing or $name or $email are valid etc.
        // return valid status code or throw an exception
        // depends on the concrete implementation

        $entity = $this->getEntity($entityClass, $id);
        $entity->set($data);
//        $product->setName(isset($data->name) ? $data->name : 'default');
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    public function resourcePatch($entityClass, $id, $data)
    {
        // handle if $id is missing or $name or $email are valid etc.
        // return valid status code or throw an exception
        // depends on the concrete implementation

        $entity = $this->getEntity($entityClass, $id);
        $entity->set($data);
        $entity->setName(isset($data->name) ? $data->name : 'default');
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    public function resourceDelete($entityClass, $id)
    {
        $entity = $this->getEntity($entityClass, $id);
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    /**
     * 嘗試取得一筆 Entity 資訊，如果沒有資料返回自訂 Exception
     * @param $entityClass
     * @param $id
     * @return object|null
     * @throws DbDataNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    protected function getEntity($entityClass, $id)
    {
        $entity = $this->getEntityManager()->find($entityClass, $id);

        try {
            if (!$entity) {
                throw new DbDataNotFoundException('Db Data Not Found', 204);
            }
        } catch (DbDataNotFoundException $e) {
            ExceptionResponse::response($e->getMessage(), $e->getCode());
        }

        return $entity;
    }
}