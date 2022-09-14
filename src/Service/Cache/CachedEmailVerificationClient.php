<?php

declare(strict_types=1);

namespace App\Service\Cache;

use App\Cache\CacheClient;

class CachedEmailVerificationClient
{
    private const SCHEMA = 'email-verification:%s';
    private const TTL = 604800;

    public function __construct(
        private readonly CacheClient $client,
    ) { }

    public function setEmail(string $email, array $response): void
    {
        $key = sprintf(self::SCHEMA, $email);
        $this->client->set($key, $response, self::TTL);
    }

    public function getEmail(string $email): ?array
    {
        $key = sprintf(self::SCHEMA, $email);

        return ($this->client->get($key)) ?: false;
    }
}
