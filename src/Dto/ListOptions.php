<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Options for list/paginated endpoints, extending {@see FetchOptions} with
 * limit and cursor support.
 *
 * The cursor is an opaque token returned by the API and should be passed
 * verbatim to the next request.
 */
final class ListOptions
{
    public function __construct(
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
