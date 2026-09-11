<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for LinkedIn company endpoint.
 *
 * @see https://docs.socialkit.dev/api-reference/linkedin-company-api
 */
final class CompanyRequest
{
    public function __construct(
        public readonly string $url,
        public readonly bool $cache = false,
        public readonly int $cacheTtl = 2_592_000,
    ) {
        if ($cacheTtl < 3600 || $cacheTtl > 2_592_000) {
            throw new \InvalidArgumentException('cacheTtl must be between 3600 and 2592000 seconds.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'cache' => $this->cache,
            'cache_ttl' => $this->cacheTtl,
        ];
    }
}
