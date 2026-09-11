<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a single YouTube search result item.
 */
final class YouTubeSearchResultItem
{
    public function __construct(
        public readonly ?string $videoId = null,
        public readonly ?string $title = null,
        public readonly ?string $thumbnail = null,
        public readonly ?string $channelName = null,
        public readonly ?string $channelId = null,
        public readonly ?string $channelUrl = null,
        public readonly ?string $publishedTime = null,
        public readonly ?string $duration = null,
        public readonly ?int $views = null,
        public readonly ?string $viewsFormatted = null,
        public readonly ?string $description = null,
        public readonly ?string $url = null,
        public readonly ?bool $verified = null,
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
            channelId: isset($data['channel_id']) ? (string) $data['channel_id'] : null,
            channelUrl: isset($data['channel_url']) ? (string) $data['channel_url'] : null,
            publishedTime: isset($data['published_time']) ? (string) $data['published_time'] : null,
            duration: isset($data['duration']) ? (string) $data['duration'] : null,
            views: isset($data['views']) ? (int) $data['views'] : null,
            viewsFormatted: isset($data['views_formatted']) ? (string) $data['views_formatted'] : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            url: isset($data['url']) ? (string) $data['url'] : null,
            verified: isset($data['verified']) ? (bool) $data['verified'] : null,
            raw: $data,
        );
    }
}
