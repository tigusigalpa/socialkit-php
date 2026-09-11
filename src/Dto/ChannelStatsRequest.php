<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for channel stats endpoints.
 *
 * @see https://docs.socialkit.dev/api-reference/youtube-channel-stats-api
 */
final class ChannelStatsRequest
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
