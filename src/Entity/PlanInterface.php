<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface PlanInterface extends
    ResourceInterface,
    ToggleableInterface,
    TimestampableInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void;

    /**
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * @param float|null $price
     */
    public function setPrice(?float $price): void;

    /**
     * @return string|null
     */
    public function getPaypalId(): ?string;

    /**
     * @param string|null $paypalId
     */
    public function setPaypalId(?string $paypalId): void;

    /**
     * @return Collection|ProductInterface[]
     */
    public function getProducts(): Collection;

    /**
     * @param ProductInterface $product
     * @return bool
     */
    public function hasProduct(ProductInterface $product): bool;

    /**
     * @param ProductInterface $product
     */
    public function addProduct(ProductInterface $product): void;

    /**
     * @param ProductInterface $product
     */
    public function removeProduct(ProductInterface $product): void;
}
