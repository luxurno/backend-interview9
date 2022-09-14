<?php

namespace App\Service;

use App\Service\Cache\CachedEmailVerificationClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailVerificationClient
{
    private string $verifierUrl = 'email-verifier';

    public function __construct(
        private CachedEmailVerificationClient $cachedClient,
        private HttpClientInterface $httpClient,
    ) { }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function verify(string $email): array
    {
        $cached = $this->cachedClient->getEmail($email);

        if ($cached) {
            return $cached;
        }

        $response = $this->httpClient->request('GET', $this->verifierUrl, [
            'query' => ['email' => $email],
        ]);
        $this->cachedClient->setEmail($email, $response->toArray());

        return $response->toArray();
    }
}
