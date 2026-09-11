<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a Facebook reaction (name + count).
 */
final class Reaction
{
    public function __construct(
        public readonly string $name,
        public readonly int $count,
        public readonly ?string $formatted = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            count: (int) ($data['count'] ?? 0),
            formatted: isset($data['formatted']) ? (string) $data['formatted'] : null,
            raw: $data,
        );
    }
}

/**
 * Represents Facebook post stats.
 */
final class FacebookStats
{
    /**
     * @param list<Reaction>         $reactions
     * @param array<string, mixed>   $raw
     */
    public function __construct(
        public readonly string $postUrl,
        public readonly ?string $id = null,
        public readonly ?string $description = null,
        public readonly ?int $views = null,
        public readonly ?int $likes = null,
        public readonly ?int $comments = null,
        public readonly ?int $shares = null,
        public readonly array $reactions = [],
        public readonly ?string $author = null,
        public readonly ?string $authorLink = null,
        public readonly ?bool $isVideo = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $reactions = [];
        foreach (($data['reactions'] ?? []) as $reaction) {
            if (is_array($reaction)) {
                $reactions[] = Reaction::fromArray($reaction);
            }
        }

        return new self(
            postUrl: (string) ($data['post_url'] ?? $data['url'] ?? ''),
            id: isset($data['id']) ? (string) $data['id'] : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            views: isset($data['views']) ? (int) $data['views'] : null,
            likes: isset($data['likes']) ? (int) $data['likes'] : null,
            comments: isset($data['comments']) ? (int) $data['comments'] : null,
            shares: isset($data['shares']) ? (int) $data['shares'] : null,
            reactions: $reactions,
            author: isset($data['author']) ? (string) $data['author'] : null,
            authorLink: isset($data['author_link']) ? (string) $data['author_link'] : null,
            isVideo: isset($data['is_video']) ? (bool) $data['is_video'] : null,
            raw: $data,
        );
    }
}
