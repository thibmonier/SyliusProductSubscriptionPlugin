<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Form\Type;

use Odiseo\SyliusProductSubscriptionPlugin\Entity\Plan;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PlanType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name', TextType::class, [
                'label' => 'sylius.ui.name',
            ])
            ->add('description', TextType::class, [
                'label' => 'sylius.ui.description',
            ])
            ->add('price', NumberType::class, [
                'label' => 'sylius.ui.price',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => Plan::PLAN_TYPES
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $plan = $event->getData();
            $form = $event->getForm();

            if ($plan && null !== $plan->getId()) {
                $form->remove('name');
                $form->remove('description');
                $form->remove('type');
            }
        });
    }
}
