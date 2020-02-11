<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PlanRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function findByEnabledQueryBuilder(): QueryBuilder;
}
