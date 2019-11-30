<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\EntityRepository;


class ProductRepository extends EntityRepository
{
    public function getById($id): ?Product
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder
            ->where('a.id = :id')
            ->setParameter(':id', $id);
        ;
//        return $queryBuilder->getQuery()->getSingleResult();
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}