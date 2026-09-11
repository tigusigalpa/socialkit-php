<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for Instagram channel posts endpoint.
 *
 * @see https://docs.socialkit.dev/api-reference/instagram-channel-posts-api
 */
final class ChannelPostsRequest
{
    public function __construct(
        public readonly string $url,
        public readonly ?int $limit = null,
        public readonly ?string $cursor = null,
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
        $arr = [
            'url' => $this->url,
            'cache' => $this->cache,
            'cache_ttl' => $this->cacheTtl,
        ];

        if ($this->limit !== null) {
            $arr['limit'] = $this->limit;
        }

        if ($this->cursor !== null) {
            $arr['cursor'] = $this->cursor;
        }

        return $arr;
    }
}
