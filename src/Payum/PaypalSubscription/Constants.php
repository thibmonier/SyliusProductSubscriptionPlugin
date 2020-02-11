<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Payum\PaypalSubscription;

abstract class Constants
{
    const FIELD_STATUS = 'status';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_APPROVAL_PENDING = 'APPROVAL_PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_SUSPENDED = 'SUSPENDED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_EXPIRED = 'EXPIRED';

    final private function __construct()
    {
    }
}
