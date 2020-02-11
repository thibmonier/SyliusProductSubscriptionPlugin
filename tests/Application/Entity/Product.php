<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusProductSubscriptionPlugin\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanAwareInterface;
use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanTrait;
use Sylius\Component\Core\Model\Product as BaseProduct;

/**
 * @ORM\Table(name="sylius_product")
 * @ORM\Entity
 */
class Product extends BaseProduct implements PlanAwareInterface
{
    use PlanTrait;
}
