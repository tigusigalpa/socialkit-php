<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents TikTok video stats.
 */
final class TikTokStats
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $videoId = null,
        public readonly ?string $title = null,
        public readonly ?string $channelName = null,
        public readonly ?string $channelLink = null,
        public readonly ?int $likes = null,
        public readonly ?int $comments = null,
        public readonly ?int $collects = null,
        public readonly ?int $shares = null,
        public readonly ?int $views = null,
        public readonly ?string $description = null,
        public readonly ?string $duration = null,
        public readonly ?string $thumbnailUrl = null,
        public readonly ?string $publishedAt = null,
        public readonly ?string $contentType = null,
        public readonly ?bool $isShortForm = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: (string) ($data['url'] ?? ''),
            videoId: isset($data['video_id']) ? (string) $data['video_id'] : null,
            title: isset($data['title']) ? (string) $data['title'] : null,
            channelName: isset($data['channel_name']) ? (string) $data['channel_name'] : null,
            channelLink: isset($data['channel_link']) ? (string) $data['channel_link'] : null,
            likes: isset($data['likes']) ? (int) $data['likes'] : null,
            comments: isset($data['comments']) ? (int) $data['comments'] : null,
            collects: isset($data['collects']) ? (int) $data['collects'] : null,
            shares: isset($data['shares']) ? (int) $data['shares'] : null,
            views: isset($data['views']) ? (int) $data['views'] : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            duration: isset($data['duration']) ? (string) $data['duration'] : null,
            thumbnailUrl: isset($data['thumbnail_url']) ? (string) $data['thumbnail_url'] : null,
            publishedAt: isset($data['published_at']) ? (string) $data['published_at'] : null,
            contentType: isset($data['content_type']) ? (string) $data['content_type'] : null,
            isShortForm: isset($data['is_short_form']) ? (bool) $data['is_short_form'] : null,
            raw: $data,
        );
    }
}
