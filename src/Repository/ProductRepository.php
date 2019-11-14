<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    public function getById($id)
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder
            ->where('a.id = ?1')
            ->setParameter(1, $id);
        ;

        return $queryBuilder->getQuery()->getSingleResult();
    }
}