<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Mapping;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanAwareInterface;
use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;

final class PlanAwareListener implements EventSubscriber
{
    /** @var RegistryInterface */
    private $resourceMetadataRegistry;

    /** @var string */
    private $planClass;

    /** @var string */
    private $productClass;

    public function __construct(
        RegistryInterface $resourceMetadataRegistry,
        string $planClass,
        string $productClass
    ) {
        $this->resourceMetadataRegistry = $resourceMetadataRegistry;
        $this->planClass = $planClass;
        $this->productClass = $productClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $reflection = $classMetadata->reflClass;

        if (!$reflection instanceof \ReflectionClass || $reflection->isAbstract()) {
            return;
        }

        if (
            $reflection->implementsInterface(ProductInterface::class) &&
            $reflection->implementsInterface(PlanAwareInterface::class)
        ) {
            $this->mapPlanAware($classMetadata, 'product_id', 'products');
        }

        if ($reflection->implementsInterface(PlanInterface::class) &&
            !$classMetadata->isMappedSuperclass
        ) {
            $this->mapPlan($classMetadata);
        }
    }

    /**
     * @param ClassMetadata $metadata
     * @param string $joinColumn
     * @param string $inversedBy
     */
    private function mapPlanAware(ClassMetadata $metadata, string $joinColumn, string $inversedBy): void
    {
        try {
            $planMetadata = $this->resourceMetadataRegistry->getByClass($this->planClass);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        if (!$metadata->hasAssociation('plans')) {
            $metadata->mapManyToOne([
                'fieldName' => 'plan',
                'targetEntity' => $planMetadata->getClass('model'),
                'inversedBy' => $inversedBy,
                'joinColumn' => [
                    'name' => $joinColumn,
                    'referencedColumnName' => 'id'
                ]
            ]);
        }
    }

    /**
     * @param ClassMetadata $metadata
     */
    private function mapPlan(ClassMetadata $metadata): void
    {
        try {
            $productMetadata = $this->resourceMetadataRegistry->getByClass($this->productClass);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        if (!$metadata->hasAssociation('products')) {
            $productConfig = [
                'fieldName' => 'products',
                'targetEntity' => $productMetadata->getClass('model')
            ];

            if (Product::class != $this->productClass) {
                $productConfig = array_merge($productConfig, [
                    'mappedBy' => 'plan',
                ]);
            }

            $metadata->mapOneToMany($productConfig);
        }
    }
}
