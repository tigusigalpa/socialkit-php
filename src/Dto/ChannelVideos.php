<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a single TikTok video item in channel videos.
 */
final class TikTokVideoItem
{
    public function __construct(
        public readonly ?string $videoId = null,
        public readonly ?string $description = null,
        public readonly ?string $url = null,
        public readonly ?string $thumbnail = null,
        public readonly ?string $duration = null,
        public readonly ?string $createTime = null,
        public readonly ?int $views = null,
        public readonly ?int $likes = null,
        public readonly ?int $comments = null,
        public readonly ?int $shares = null,
        public readonly ?int $collects = null,
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
            description: isset($data['description']) ? (string) $data['description'] : null,
            url: isset($data['url']) ? (string) $data['url'] : null,
            thumbnail: isset($data['thumbnail']) ? (string) $data['thumbnail'] : null,
            duration: isset($data['duration']) ? (string) $data['duration'] : null,
            createTime: isset($data['create_time']) ? (string) $data['create_time'] : null,
            views: isset($data['views']) ? (int) $data['views'] : null,
            likes: isset($data['likes']) ? (int) $data['likes'] : null,
            comments: isset($data['comments']) ? (int) $data['comments'] : null,
            shares: isset($data['shares']) ? (int) $data['shares'] : null,
            collects: isset($data['collects']) ? (int) $data['collects'] : null,
            raw: $data,
        );
    }
}

/**
 * Represents TikTok channel videos response.
 */
final class ChannelVideos
{
    /**
     * @param list<TikTokVideoItem>  $results
     * @param array<string, mixed>   $raw
     */
    public function __construct(
        public readonly string $profileUrl,
        public readonly ?string $channelName = null,
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
        $results = [];
        foreach (($data['results'] ?? []) as $item) {
            if (is_array($item)) {
                $results[] = TikTokVideoItem::fromArray($item);
            }
        }

        return new self(
            profileUrl: (string) ($data['profile_url'] ?? $data['url'] ?? ''),
            channelName: isset($data['channel_name']) ? (string) $data['channel_name'] : null,
            results: $results,
            hasMore: isset($data['has_more']) ? (bool) $data['has_more'] : null,
            cursor: isset($data['cursor']) ? (string) $data['cursor'] : null,
            raw: $data,
        );
    }
}
