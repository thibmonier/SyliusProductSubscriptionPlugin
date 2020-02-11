<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Payum\PaypalSubscription\Action;

use Odiseo\SyliusProductSubscriptionPlugin\Paypal\Api\Subscription;
use Odiseo\SyliusProductSubscriptionPlugin\Payum\PaypalSubscription\Constants;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;

final class StatusAction implements ActionInterface, GatewayAwareInterface
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
        /** @var GetStatusInterface $request */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getFirstModel();
        $paymentDetails = $payment->getDetails();

        if (empty($paymentDetails) || !isset($paymentDetails['id'])) {
            $request->markNew();

            return;
        }

        $this->gateway->execute($httpRequest = new GetHttpRequest());
        $subscriptionId = isset($httpRequest->query['subscription_id'])
            ? $httpRequest->query['subscription_id'] : $paymentDetails['id']
        ;

        $response = $this->subscription->showSubscription($subscriptionId);

        $payment->setDetails($response);

        $this->setStatus($response, $request);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getFirstModel() instanceof SyliusPaymentInterface
        ;
    }

    /**
     * @param array $response
     * @param GetStatusInterface $request
     */
    private function setStatus(array $response, GetStatusInterface $request): void
    {
        if (isset($response[Constants::FIELD_STATUS])) {
            if (Constants::STATUS_ACTIVE === $response[Constants::FIELD_STATUS]) {
                $request->markCaptured();

                return;
            } elseif (Constants::STATUS_APPROVAL_PENDING === $response[Constants::FIELD_STATUS]) {
                $request->markPending();

                return;
            } elseif (Constants::STATUS_CANCELLED === $response[Constants::FIELD_STATUS]) {
                $request->markCanceled();

                return;
            } elseif (Constants::STATUS_SUSPENDED === $response[Constants::FIELD_STATUS]) {
                $request->markSuspended();

                return;
            } elseif (Constants::STATUS_EXPIRED === $response[Constants::FIELD_STATUS]) {
                $request->markExpired();

                return;
            }

            $request->markFailed();

            return;
        }

        $request->markUnknown();
    }
}
