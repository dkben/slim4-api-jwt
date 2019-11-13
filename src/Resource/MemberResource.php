<?php


namespace App\Resource;


use App\Entity\Member;
use App\Helper\RedisHelper;
use App\Helper\SaveLogHelper;


class MemberResource extends BaseResource
{
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
            $member = $this->getEntityManager()->getRepository('App\Entity\Member')->findAll();
            $member = array_map(function($member) {
                return $this->convertToArray($member); },
                $member);
            $data = $member;
        } else {
            $member = $this->getEntityManager()->find('\App\Entity\Member', $id);
            $data = (is_null($member)) ? '' : $this->convertToArray($member);
        }

        // @TODO handle correct status when no data is found...

        return json_encode($data);
    }

    // POST, PUT, DELETE methods...
    public function post($data)
    {
        /** @var Member $member */
        $member = new Member();
        $member->setName($data->name);
        $this->getEntityManager()->persist($member);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($member));
    }

    public function put($id, $data)
    {
        // handle if $id is missing or $name or $email are valid etc.
        // return valid status code or throw an exception
        // depends on the concrete implementation

        /** @var Member $member */
        $member = $this->getEntityManager()->find('App\Entity\Member', $id);
        $member->setName($data->name);
        $this->getEntityManager()->persist($member);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($member));
    }

    public function patch($id, $data)
    {
        // handle if $id is missing or $name or $email are valid etc.
        // return valid status code or throw an exception
        // depends on the concrete implementation

        /** @var Member $member */
        $member = $this->getEntityManager()->find('App\Entity\Member', $id);
        $member->setName($data->name);
        $this->getEntityManager()->persist($member);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($member));
    }

    public function delete($id, $data)
    {
        $member = $this->getEntityManager()->find('App\Entity\Member', $id);

        $this->getEntityManager()->remove($member);
        $this->getEntityManager()->flush();
        return json_encode($this->convertToArray($member));
    }

    private function convertToArray(Member $member) {
        return array(
            'id' => $member->getId(),
            'name' => $member->getName(),
            'email' => $member->getEmail(),
            'password' => $member->getPassword(),
//            'createdAt' => date('Y-m-d H:i:s', $member->getCreatedAt()),
//            'updatedAt' => date('Y-m-d H:i:s', $member->getUpdatedAt())
        );
    }
}