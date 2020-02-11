<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Paypal\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

final class Product
{
    /** @var Authorization */
    private $authorization;

    /** @var Client */
    private $client;

    public function __construct(Authorization $authorization, bool $sandbox)
    {
        $this->authorization = $authorization;

        $baseUri = 'https://api.paypal.com';

        if ($sandbox) {
            $baseUri = 'https://api.sandbox.paypal.com';
        }

        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 2.0,
        ]);
    }

    /**
     * @return array
     */
    public function listProducts(): array
    {
        try {
            if ($this->authorization->getAccessToken() === null) {
                $this->authorization->setAccessToken();
            }

            $token = $this->authorization->getAccessToken();

            $uri = '/v1/catalogs/products';

            $response = $this->client->get($uri, [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $message = '{}';
            if (null !== $e->getResponse()) {
                $message = $e->getResponse()->getBody()->getContents();
            }

            return json_decode($message, true);
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function createProduct(array $data): array
    {
        try {
            if ($this->authorization->getAccessToken() === null) {
                $this->authorization->setAccessToken();
            }

            $token = $this->authorization->getAccessToken();

            $uri = '/v1/catalogs/products';

            $response = $this->client->post($uri, [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::BODY => json_encode($data)
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $message = '{}';
            if (null !== $e->getResponse()) {
                $message = $e->getResponse()->getBody()->getContents();
            }

            return json_decode($message, true);
        }
    }
}
