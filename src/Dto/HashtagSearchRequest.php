<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for TikTok hashtag search endpoint.
 *
 * The hashtag should be provided without the leading `#`.
 *
 * @see https://docs.socialkit.dev/api-reference/tiktok-hashtag-search-api
 */
final class HashtagSearchRequest
{
    public function __construct(
        public readonly string $hashtag,
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
            'hashtag' => $this->hashtag,
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
