<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class MemberRepository extends EntityRepository
{
    public function getById($id)
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder
            ->where('a.id = ?1')
            ->setParameter(1, $id)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function getByEmail($email)
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder
            ->where('a.email = ?1')
            ->setParameter(1, $email)
        ;

        return $queryBuilder->getQuery()->getSingleResult();
    }

}