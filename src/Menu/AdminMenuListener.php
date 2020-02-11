<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        /** @var ItemInterface $item */
        $item = $menu->getChild('catalog');
        if (null == $item) {
            $item = $menu;
        }

        $item->addChild('plans', ['route' => 'odiseo_sylius_product_subscription_plugin_admin_plan_index'])
            ->setLabel('odiseo_sylius_product_subscription_plugin.menu.admin.plans')
            ->setLabelAttribute('icon', 'percent')
        ;
    }
}
