<?php

declare(strict_types=1);

namespace Odiseo\SyliusProductSubscriptionPlugin\Paypal\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

final class Authorization
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $accessToken = null;

    /** @var Client */
    private $client;

    public function __construct(string $clientId, string $clientSecret, bool $sandbox)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $baseUri = 'https://api.paypal.com';

        if ($sandbox) {
            $baseUri = 'https://api.sandbox.paypal.com';
        }

        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 2.0,
        ]);
    }

    public function setAccessToken(): void
    {
        try {
            $uri = '/v1/oauth2/token';

            $response = $this->client->post($uri, [
                RequestOptions::HEADERS => ['Accept' => 'application/json', 'Accept-Language' => 'en_US'],
                RequestOptions::AUTH => [$this->clientId, $this->clientSecret],
                RequestOptions::FORM_PARAMS => ['grant_type' => 'client_credentials']
            ]);

            $content = json_decode($response->getBody()->getContents(), true);

            $this->accessToken = $content['access_token'];
        } catch (RequestException $exception) {
        }
    }

    /**
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }
}
