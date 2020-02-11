<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Form\Extension;

use Odiseo\SyliusProductSubscriptionPlugin\Form\Type\PlanChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plan', PlanChoiceType::class, [
            'label' => 'odiseo_sylius_product_subscription_plugin.form.product.select_plan',
            'required' => false
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}
