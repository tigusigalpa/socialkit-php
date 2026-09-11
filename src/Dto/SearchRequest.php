<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for search endpoints.
 *
 * @see https://docs.socialkit.dev/api-reference/youtube-search-api
 */
final class SearchRequest
{
    public function __construct(
        public readonly string $query,
        public readonly ?int $limit = null,
        public readonly ?string $sortBy = null,
        public readonly ?string $uploadDate = null,
        public readonly ?string $datePosted = null,
        public readonly ?string $type = null,
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
            'query' => $this->query,
            'cache' => $this->cache,
            'cache_ttl' => $this->cacheTtl,
        ];

        if ($this->limit !== null) {
            $arr['limit'] = $this->limit;
        }

        if ($this->sortBy !== null) {
            $arr['sort_by'] = $this->sortBy;
        }

        if ($this->uploadDate !== null) {
            $arr['upload_date'] = $this->uploadDate;
        }

        if ($this->datePosted !== null) {
            $arr['date_posted'] = $this->datePosted;
        }

        if ($this->type !== null) {
            $arr['type'] = $this->type;
        }

        return $arr;
    }
}
