<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;

class   Plan implements PlanInterface
{
    const PLAN_TYPES = [
        'DAY' => 'DAY',
        'WEEK' => 'WEEK',
        'MONTH' => 'MONTH',
        'YEAR' => 'YEAR'
    ];

    use TimestampableTrait;
    use ToggleableTrait;

    /** @var int|null */
    private $id;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $type;

    /** @var float|null */
    private $price;

    /** @var string|null */
    private $paypalId;

    /** @var Collection|ProductInterface[] */
    protected $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaypalId(): ?string
    {
        return $this->paypalId;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalId(?string $paypalId): void
    {
        $this->paypalId = $paypalId;
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProduct(ProductInterface $product): bool
    {
        return $this->products->contains($product);
    }

    /**
     * {@inheritdoc}
     */
    public function addProduct(ProductInterface $product): void
    {
        if (!$this->hasProduct($product)) {
            $this->products->add($product);

            if ($product instanceof PlanAwareInterface) {
                $product->setPlan($this);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeProduct(ProductInterface $product): void
    {
        if ($this->hasProduct($product)) {
            $this->products->removeElement($product);

            if ($product instanceof PlanAwareInterface) {
                $product->setPlan(null);
            }
        }
    }
}
