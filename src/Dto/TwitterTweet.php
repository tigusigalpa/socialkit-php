<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a single tweet.
 */
final class TwitterTweet
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $text = null,
        public readonly ?int $likes = null,
        public readonly ?int $retweets = null,
        public readonly ?int $replies = null,
        public readonly ?int $views = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $author = null,
        public readonly array $hashtags = [],
        public readonly array $urls = [],
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
            text: isset($data['text']) ? (string) $data['text'] : null,
            likes: isset($data['likes']) ? (int) $data['likes'] : null,
            retweets: isset($data['retweets']) ? (int) $data['retweets'] : null,
            replies: isset($data['replies']) ? (int) $data['replies'] : null,
            views: isset($data['views']) ? (int) $data['views'] : null,
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
            author: isset($data['author']) ? (string) $data['author'] : null,
            hashtags: array_values((array) ($data['hashtags'] ?? [])),
            urls: array_values((array) ($data['urls'] ?? [])),
            raw: $data,
        );
    }
}
