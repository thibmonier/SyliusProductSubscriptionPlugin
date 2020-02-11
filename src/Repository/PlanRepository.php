<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class PlanRepository extends EntityRepository implements PlanRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByEnabledQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('enabled', true)
        ;
    }
}
