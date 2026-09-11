<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a search response with pagination support.
 */
final class SearchResult
{
    /**
     * @param list<mixed>           $results
     * @param array<string, mixed>  $raw
     */
    public function __construct(
        public readonly string $query,
        public readonly array $results = [],
        public readonly ?bool $hasMore = null,
        public readonly ?string $cursor = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            query: (string) ($data['query'] ?? ''),
            results: array_values((array) ($data['results'] ?? [])),
            hasMore: isset($data['has_more']) ? (bool) $data['has_more'] : null,
            cursor: isset($data['cursor']) ? (string) $data['cursor'] : null,
            raw: $data,
        );
    }
}
