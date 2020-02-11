<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Controller;

use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanInterface;
use Odiseo\SyliusProductSubscriptionPlugin\Paypal\Api\Plan;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class PlanController extends ResourceController
{
    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function enableAction(int $id): RedirectResponse
    {
        /** @var Plan $apiPlan */
        $apiPlan = $this->get('odiseo_sylius_product_subscription_plugin.paypal.api.plan');

        /** @var PlanInterface $subscriptionPlan */
        $subscriptionPlan = $this->repository->find($id);

        $apiPlan->activatePlan($subscriptionPlan->getPaypalId());

        $subscriptionPlan->setEnabled(true);

        $this->manager->flush();

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');

        $this->addFlash('success', $translator->trans('sylius.ui.updated'));

        return $this->redirectToRoute('odiseo_sylius_product_subscription_plugin_admin_plan_index');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function disableAction($id): RedirectResponse
    {
        /** @var Plan $apiPlan */
        $apiPlan = $this->get('odiseo_sylius_product_subscription_plugin.paypal.api.plan');

        /** @var PlanInterface $subscriptionPlan */
        $subscriptionPlan = $this->repository->find($id);

        $apiPlan->deactivatePlan($subscriptionPlan->getPaypalId());

        $subscriptionPlan->setEnabled(false);

        $this->manager->flush();

        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');

        $this->addFlash('success', $translator->trans('sylius.ui.updated'));

        return $this->redirectToRoute('odiseo_sylius_product_subscription_plugin_admin_plan_index');
    }
}
