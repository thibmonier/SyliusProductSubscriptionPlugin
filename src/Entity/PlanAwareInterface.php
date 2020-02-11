<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Entity;

interface PlanAwareInterface
{
    /**
     * @return PlanInterface|null
     */
    public function getPlan(): ?PlanInterface;

    /**
     * @param PlanInterface|null $plan
     */
    public function setPlan(?PlanInterface $plan): void;
}
