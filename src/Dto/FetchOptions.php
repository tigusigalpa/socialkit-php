<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Common fetch options shared by most request DTOs.
 *
 * Controls server-side caching behaviour for scrape/read operations.
 */
final class FetchOptions
{
    /**
     * @param bool $cache    Whether to use cached results when available.
     * @param int  $cacheTtl Cache time-to-live in seconds (3600–2592000).
     *
     * @throws \InvalidArgumentException If cacheTtl is outside the allowed range.
     */
    public function __construct(
        public readonly bool $cache = false,
        public readonly int $cacheTtl = 2_592_000,
    ) {
        if ($cacheTtl < 3600 || $cacheTtl > 2_592_000) {
            throw new \InvalidArgumentException('cacheTtl must be between 3600 and 2592000 seconds.');
        }
    }

    /**
     * Serialise the options to an associative array suitable for JSON encoding.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'cache' => $this->cache,
            'cache_ttl' => $this->cacheTtl,
        ];
    }
}
