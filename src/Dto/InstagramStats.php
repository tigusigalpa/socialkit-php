<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents Instagram post stats.
 */
final class InstagramStats
{
    public function __construct(
        public readonly string $postUrl,
        public readonly ?string $id = null,
        public readonly ?string $shortcode = null,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?int $views = null,
        public readonly ?int $likes = null,
        public readonly ?int $comments = null,
        public readonly ?string $publishedAt = null,
        public readonly ?string $author = null,
        public readonly ?string $authorLink = null,
        public readonly ?string $duration = null,
        public readonly ?string $thumbnail = null,
        public readonly ?bool $isVideo = null,
        public readonly ?string $contentType = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            postUrl: (string) ($data['post_url'] ?? $data['url'] ?? ''),
            id: isset($data['id']) ? (string) $data['id'] : null,
            shortcode: isset($data['shortcode']) ? (string) $data['shortcode'] : null,
            title: isset($data['title']) ? (string) $data['title'] : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            views: isset($data['views']) ? (int) $data['views'] : null,
            likes: isset($data['likes']) ? (int) $data['likes'] : null,
            comments: isset($data['comments']) ? (int) $data['comments'] : null,
            publishedAt: isset($data['published_at']) ? (string) $data['published_at'] : null,
            author: isset($data['author']) ? (string) $data['author'] : null,
            authorLink: isset($data['author_link']) ? (string) $data['author_link'] : null,
            duration: isset($data['duration']) ? (string) $data['duration'] : null,
            thumbnail: isset($data['thumbnail']) ? (string) $data['thumbnail'] : null,
            isVideo: isset($data['is_video']) ? (bool) $data['is_video'] : null,
            contentType: isset($data['content_type']) ? (string) $data['content_type'] : null,
            raw: $data,
        );
    }
}
