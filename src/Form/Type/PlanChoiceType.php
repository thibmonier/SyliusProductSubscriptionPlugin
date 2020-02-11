<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Form\Type;

use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanInterface;
use Odiseo\SyliusProductSubscriptionPlugin\Repository\PlanRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlanChoiceType extends AbstractType
{
    /** @var PlanRepositoryInterface */
    private $planRepository;

    public function __construct(
        PlanRepositoryInterface $planRepository
    ) {
        $this->planRepository = $planRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $criteria = [];
        $orderBy = ['name' => 'ASC'];

        $resolver->setDefaults([
            'choices' => function (Options $options) use ($criteria, $orderBy): array {
                $plans = $this->planRepository->findBy($criteria, $orderBy);

                $choices = [];
                /** @var PlanInterface $plan */
                foreach ($plans as $plan) {
                    $choices[$plan->getName()] = $plan;
                }

                return $choices;
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'odiseo_sylius_plan_choice';
    }
}
