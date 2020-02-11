<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanInterface;
use Odiseo\SyliusProductSubscriptionPlugin\Paypal\SubscriptionManager;

final class PlanSubscriber implements EventSubscriber
{
    /** @var SubscriptionManager */
    private $subscriptionManager;

    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $plan = $args->getObject();

        if (!$plan instanceof PlanInterface) {
            return;
        }

        $data = [
            'name' => $plan->getName(),
            'description' => $plan->getDescription(),
            'type' => $plan->getType(),
            'price' => $plan->getPrice()
        ];

        $billingPlan = $this->subscriptionManager->newPlan($data);

        $plan->setPaypalId($billingPlan['id']);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $plan = $args->getObject();

        if (!$plan instanceof PlanInterface) {
            return;
        }

        $paypalId = $plan->getPaypalId();

        $data = [
            'price' => $plan->getPrice()
        ];

        $this->subscriptionManager->editPlanPrice($paypalId, $data);
    }
}
