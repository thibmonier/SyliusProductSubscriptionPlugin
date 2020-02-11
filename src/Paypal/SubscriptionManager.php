<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Paypal;

use Odiseo\SyliusProductSubscriptionPlugin\Paypal\Api\Plan;
use Odiseo\SyliusProductSubscriptionPlugin\Paypal\Api\Product;

final class SubscriptionManager
{
    /** @var Product */
    private $product;

    /** @var Plan */
    private $plan;

    public function __construct(Product $product, Plan $plan)
    {
        $this->product = $product;
        $this->plan = $plan;
    }

    /**
     * @param array $billingPlan
     * @return array
     */
    public function newPlan(array $billingPlan): array
    {
        $products = $this->product->listProducts();

        $product = null;
        if (count($products['products']) > 0) {
            $product = $products['products'][0];
        }

        if ($product === null) {
            $data = [
                'name' => 'Odiseo',
                'type' => 'PHYSICAL'
            ];

            $product = $this->product->createProduct($data);
        }

        $data = [
            'product_id' => $product['id'],
            'name' => $billingPlan['name'],
            'description' => $billingPlan['description'],
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit' => $billingPlan['type'],
                        'interval_count' => 1
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => $billingPlan['price'],
                            'currency_code' => 'USD'
                        ]
                    ]
                ],
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value' => 0,
                    'currency_code' => 'USD'
                ],
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3
            ]
        ];

        return $this->plan->createPlan($data);
    }

    /**
     * @param string $planId
     * @param array $billingPlan
     * @return array
     */
    public function editPlanPrice(string $planId, array $billingPlan): array
    {
        $data = [
            'pricing_schemes' => [
                [
                    'billing_cycle_sequence' => 1,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => $billingPlan['price'],
                            'currency_code' => 'USD'
                        ]
                    ]
                ]
            ]
        ];

        return $this->plan->updatePricingSchemesPlan($planId, $data);
    }
}
