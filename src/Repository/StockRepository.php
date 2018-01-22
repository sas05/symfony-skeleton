<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for Company information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Saeed Ahmed <saeed.sas@gmail.com>
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function getHighestStock() {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT s1
                FROM App:Stock s1
                WHERE s1.price=(SELECT MAX(s2.price)
                    FROM App:Stock s2
                    WHERE s1.exchangeMarket = s2.exchangeMarket)
            ')
        ;
        $stocks = $query->getResult();

        return $stocks;
    }
}
