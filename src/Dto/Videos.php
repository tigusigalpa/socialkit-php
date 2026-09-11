<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a single YouTube video item in a videos response.
 */
final class YouTubeVideoItem
{
    public function __construct(
        public readonly ?string $videoId = null,
        public readonly ?string $title = null,
        public readonly ?string $thumbnail = null,
        public readonly ?string $channelName = null,
        public readonly ?string $duration = null,
        public readonly ?int $views = null,
        public readonly ?string $viewsFormatted = null,
        public readonly ?string $publishedTime = null,
        public readonly ?string $publishedAt = null,
        public readonly ?string $description = null,
        public readonly ?string $url = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            videoId: isset($data['video_id']) ? (string) $data['video_id'] : null,
            title: isset($data['title']) ? (string) $data['title'] : null,
            thumbnail: isset($data['thumbnail']) ? (string) $data['thumbnail'] : null,
            channelName: isset($data['channel_name']) ? (string) $data['channel_name'] : null,
            duration: isset($data['duration']) ? (string) $data['duration'] : null,
            views: isset($data['views']) ? (int) $data['views'] : null,
            viewsFormatted: isset($data['views_formatted']) ? (string) $data['views_formatted'] : null,
            publishedTime: isset($data['published_time']) ? (string) $data['published_time'] : null,
            publishedAt: isset($data['published_at']) ? (string) $data['published_at'] : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            url: isset($data['url']) ? (string) $data['url'] : null,
            raw: $data,
        );
    }
}

/**
 * Represents YouTube videos response.
 */
final class Videos
{
    /**
     * @param list<YouTubeVideoItem> $results
     * @param array<string, mixed>   $raw
     */
    public function __construct(
        public readonly string $url,
        public readonly ?string $type = null,
        public readonly array $results = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $results = [];
        foreach (($data['results'] ?? []) as $item) {
            if (is_array($item)) {
                $results[] = YouTubeVideoItem::fromArray($item);
            }
        }

        return new self(
            url: (string) ($data['url'] ?? ''),
            type: isset($data['type']) ? (string) $data['type'] : null,
            results: $results,
            raw: $data,
        );
    }
}
