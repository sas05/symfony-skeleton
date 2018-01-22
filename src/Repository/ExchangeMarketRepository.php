<?php

namespace App\Repository;

use App\Entity\ExchangeMarket;
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
class ExchangeMarketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeMarket::class);
    }
}
