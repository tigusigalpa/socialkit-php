<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for YouTube videos endpoint.
 *
 * @see https://docs.socialkit.dev/api-reference/youtube-videos-api
 */
final class VideosRequest
{
    public function __construct(
        public readonly string $url,
        public readonly ?int $limit = null,
        public readonly ?bool $fullDetails = null,
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

        if ($this->fullDetails !== null) {
            $arr['full_details'] = $this->fullDetails;
        }

        return $arr;
    }
}
