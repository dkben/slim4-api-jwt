<?php


namespace App\Resource;


use App\Entity\Admin;
use App\Helper\RedisHelper;
use App\Helper\SaveLogHelper;


class AdminResource extends BaseResource
{
    public function __construct($request, $response, $args)
    {
        parent::__construct($request, $response, $args);

//        $this->appendAuth("GET", '*');
        $this->appendAuth("GET", 'admin');
//        $this->appendAuth("GET", 'member');
//        $this->appendAuth("POST", 'admin');
//        $this->appendAuth("PUT", 'admin');
//        $this->appendAuth("PATCH", 'admin');
//        $this->appendAuth("DELETE", 'admin');

        $this->checkRolePermission($request);
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function get($id)
    {
        // 在這裡使用 Monolog 的方法
//        SaveLogHelper::save('111', 'aaa');
        // 在這裡使用 Redis 的方法
//        RedisHelper::save('slim4', 'hi4');
//        echo RedisHelper::get('slim4'); die;

        if ($id === null) {
            $admin = $this->getEntityManager()->getRepository('App\Entity\Admin')->findAll();
            $admin = array_map(function($admin) {
                return $this->convertToArray($admin); },
                $admin);
            $data = $admin;
        } else {
            $admin = $this->getEntityManager()->find('\App\Entity\Admin', $id);
            $data = (is_null($admin)) ? '' : $this->convertToArray($admin);
        }

        // @TODO handle correct status when no data is found...

        return json_encode($data);
    }

    // POST, PUT, DELETE methods...
    public function post($data)
    {
        /** @var Admin $admin */
        $admin = new Admin();
        $admin->setName($data->name);
        $this->getEntityManager()->persist($admin);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($admin));
    }

    public function put($id, $data)
    {
        // handle if $id is missing or $name or $email are valid etc.
        // return valid status code or throw an exception
        // depends on the concrete implementation

        /** @var Admin $admin */
        $admin = $this->getEntityManager()->find('App\Entity\Admin', $id);
        $admin->setName($data->name);
        $this->getEntityManager()->persist($admin);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($admin));
    }

    public function patch($id, $data)
    {
        // handle if $id is missing or $name or $email are valid etc.
        // return valid status code or throw an exception
        // depends on the concrete implementation

        /** @var Admin $admin */
        $admin = $this->getEntityManager()->find('App\Entity\Admin', $id);
        $admin->setName($data->name);
        $this->getEntityManager()->persist($admin);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($admin));
    }

    public function delete($id)
    {
        $admin = $this->getEntityManager()->find('App\Entity\Admin', $id);

        $this->getEntityManager()->remove($admin);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($admin));
    }

    private function convertToArray(Admin $admin)
    {
        return array(
            'id' => $admin->getId(),
            'name' => $admin->getName(),
            'email' => $admin->getEmail(),
            'password' => $admin->getPassword(),
//            'createdAt' => date('Y-m-d H:i:s', $admin->getCreatedAt()),
//            'updatedAt' => date('Y-m-d H:i:s', $admin->getUpdatedAt())
        );
    }
}