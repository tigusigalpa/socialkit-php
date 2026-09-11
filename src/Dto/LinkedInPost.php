<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a single LinkedIn post.
 */
final class LinkedInPost
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $text = null,
        public readonly ?int $likes = null,
        public readonly ?int $comments = null,
        public readonly ?int $shares = null,
        public readonly ?string $imageUrl = null,
        public readonly ?string $videoUrl = null,
        public readonly ?string $publishedAt = null,
        public readonly ?array $author = null,
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
            comments: isset($data['comments']) ? (int) $data['comments'] : null,
            shares: isset($data['shares']) ? (int) $data['shares'] : null,
            imageUrl: isset($data['image_url']) ? (string) $data['image_url'] : null,
            videoUrl: isset($data['video_url']) ? (string) $data['video_url'] : null,
            publishedAt: isset($data['published_at']) ? (string) $data['published_at'] : null,
            author: isset($data['author']) && is_array($data['author']) ? $data['author'] : null,
            raw: $data,
        );
    }
}
