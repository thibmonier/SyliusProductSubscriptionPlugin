<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Payum\PaypalSubscription;

use Odiseo\SyliusProductSubscriptionPlugin\Payum\PaypalSubscription\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class PaypalSubscriptionGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'paypal subscription',
            'payum.factory_title' => 'Paypal Subscription'
        ]);
    }
}
