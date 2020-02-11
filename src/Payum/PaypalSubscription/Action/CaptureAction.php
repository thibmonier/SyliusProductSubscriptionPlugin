<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Payum\PaypalSubscription\Action;

use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanInterface;
use Odiseo\SyliusProductSubscriptionPlugin\Paypal\Api\Subscription;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /** @var Subscription */
    private $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(StaticAccess)
     */
    public function execute($request): void
    {
        /** @var Capture $request */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getModel();

        $this->gateway->execute($status = new GetStatus($payment));

        if ($status->isNew()) {
            /** @var OrderInterface $order */
            $order = $payment->getOrder();

            try {
                $data = $this->getSubscriptionData($order, $request);

                $response = $this->subscription->createSubscription($data);
            } catch (\Exception $exception) {
                $response = [
                    'status' => $exception->getCode(),
                    'message' => $exception->getMessage()
                ];
            } finally {
                $payment->setDetails($response);
            }

            if (isset($response['id'])) {
                $hateoasLink = $this->getHateoasLink($response);

                throw new HttpRedirect($hateoasLink);
            }
        }

        if ($status->isPending()) {
            $paymentDetails = $payment->getDetails();

            if (isset($paymentDetails['id'])) {
                $hateoasLink = $this->getHateoasLink($paymentDetails);

                throw new HttpRedirect($hateoasLink);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }

    /**
     * @param OrderInterface $order
     * @param Capture $request
     * @return array
     */
    private function getSubscriptionData(OrderInterface $order, Capture $request): array
    {
        $data = [];

        $customer = $order->getCustomer();
        if ($customer instanceof CustomerInterface) {
            $planId = null;

            $shippingAddress = $order->getShippingAddress();

            $shippingAmount = $order->getShippingTotal();

            /** @var OrderItemInterface $firstItem */
            $item = $order->getItems()->first();

            $variant = $item->getVariant();
            if ($variant instanceof ProductVariantInterface) {
                $product = $variant->getProduct();
                if ($product instanceof ProductInterface) {
                    $plan = $product->getPlan();
                    if ($plan instanceof PlanInterface) {
                        $planId = $plan->getPaypalId();
                    }
                }
            }

            $returnUrl = '';
            $cancelUrl = '';

            $token = $request->getToken();
            if ($token instanceof TokenInterface) {
                $returnUrl = $token->getTargetUrl();
                $cancelUrl = $token->getTargetUrl();
            }

            if ($planId) {
                $data = [
                    'plan_id' => $planId,
                    'quantity' => 1,
                    'shipping_amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($shippingAmount / 100, 2)
                    ],
                    'subscriber' => [
                        'name' => [
                            'given_name' => $customer->getFirstName() ?: 'NA',
                            'surname' => $customer->getLastName() ?: 'NA'
                        ],
                        'email_address' => $customer->getEmail()
                    ],
                    'application_context' => [
                        'brand_name' => 'ODISEO',
                        'locale' => 'en-US',
                        'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                        'user_action' => 'SUBSCRIBE_NOW',
                        'payment_method' => [
                            'payer_selected' => 'PAYPAL',
                            'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'
                        ],
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl
                    ]
                ];

                if ($shippingAddress) {
                    $data['subscriber']['shipping_address'] = [
                        'name' => [
                            'full_name' => $shippingAddress->getFullName() ?: $customer->getFullName() ?: 'NA'
                        ],
                        'address' => [
                            'address_line_1' => $this->getAddressLine1($shippingAddress),
                            'address_line_2' => $this->getAddressLine2($shippingAddress),
                            'admin_area_2' => $this->getAdminArea2($shippingAddress),
                            'admin_area_1' => $this->getAdminArea1($shippingAddress),
                            'postal_code' => $this->getPostalCode($shippingAddress),
                            'country_code' => $this->getCountryCode($shippingAddress)
                        ]
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * @param AddressInterface $shippingAddress
     * @return string
     */
    private function getAddressLine1(AddressInterface $shippingAddress): string
    {
        return $shippingAddress->getStreet() ?: 'NA';
    }

    /**
     * @param AddressInterface $shippingAddress
     * @return string
     */
    private function getAddressLine2(AddressInterface $shippingAddress): string
    {
        return $shippingAddress->getCity() ?: 'NA';
    }

    /**
     * @param AddressInterface $shippingAddress
     * @return string
     */
    private function getAdminArea1(AddressInterface $shippingAddress): string
    {
        return $shippingAddress->getProvinceName() ?: 'NA';
    }

    /**
     * @param AddressInterface $shippingAddress
     * @return string
     */
    private function getAdminArea2(AddressInterface $shippingAddress): string
    {
        return $shippingAddress->getProvinceCode() ?: 'NA';
    }

    /**
     * @param AddressInterface $shippingAddress
     * @return string
     */
    private function getPostalCode(AddressInterface $shippingAddress): string
    {
        return $shippingAddress->getPostcode() ?: 'NA';
    }

    /**
     * @param AddressInterface $shippingAddress
     * @return string
     */
    private function getCountryCode(AddressInterface $shippingAddress): string
    {
        return $shippingAddress->getCountryCode() ?: 'NA';
    }

    /**
     * @param array $paymentDetails
     * @return string
     */
    private function getHateoasLink(array $paymentDetails): string
    {
        $hateoasLink = '';

        $links = $paymentDetails['links'];
        foreach ($links as $link) {
            if ($link['rel'] === 'approve') {
                $hateoasLink = $link['href'];
                break;
            }
        }

        return $hateoasLink;
    }
}
