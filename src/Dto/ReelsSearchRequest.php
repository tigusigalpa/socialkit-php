<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

/**
 * Request DTO for Instagram reels search endpoint.
 *
 * The page parameter must be 1 — the API only supports the first page.
 * Validation is performed locally before the request is sent.
 *
 * @see https://docs.socialkit.dev/api-reference/instagram-reels-search-api
 */
final class ReelsSearchRequest
{
    /**
     * @param string $query Search query.
     * @param int|null $limit Maximum results.
     * @param int $page Page number (must be 1).
     *
     * @throws BadRequestException If page is greater than 1.
     */
    public function __construct(
        public readonly string $query,
        public readonly ?int $limit = null,
        public readonly int $page = 1,
        public readonly bool $cache = false,
        public readonly int $cacheTtl = 2_592_000,
    ) {
        if ($cacheTtl < 3600 || $cacheTtl > 2_592_000) {
            throw new \InvalidArgumentException('cacheTtl must be between 3600 and 2592000 seconds.');
        }

        if ($page > 1) {
            throw new BadRequestException('Instagram reels search only supports page 1.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $arr = [
            'query' => $this->query,
            'page' => $this->page,
            'cache' => $this->cache,
            'cache_ttl' => $this->cacheTtl,
        ];

        if ($this->limit !== null) {
            $arr['limit'] = $this->limit;
        }

        return $arr;
    }
}
