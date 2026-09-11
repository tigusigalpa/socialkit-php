<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a single TikTok search result item.
 */
final class TikTokSearchResultItem
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $desc = null,
        public readonly ?string $url = null,
        public readonly ?string $createTime = null,
        public readonly ?string $author = null,
        public readonly array $stats = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (string) $data['id'] : null,
            desc: isset($data['desc']) ? (string) $data['desc'] : null,
            url: isset($data['url']) ? (string) $data['url'] : null,
            createTime: isset($data['create_time']) ? (string) $data['create_time'] : null,
            author: isset($data['author']) ? (string) $data['author'] : null,
            stats: (array) ($data['stats'] ?? []),
            raw: $data,
        );
    }
}

/**
 * Represents a single TikTok hashtag search result item.
 */
final class TikTokHashtagResultItem
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $desc = null,
        public readonly ?string $url = null,
        public readonly ?string $author = null,
        public readonly array $stats = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (string) $data['id'] : null,
            desc: isset($data['desc']) ? (string) $data['desc'] : null,
            url: isset($data['url']) ? (string) $data['url'] : null,
            author: isset($data['author']) ? (string) $data['author'] : null,
            stats: (array) ($data['stats'] ?? []),
            raw: $data,
        );
    }
}
