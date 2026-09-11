<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a single Instagram post item in channel posts.
 */
final class InstagramPostItem
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $shortcode = null,
        public readonly ?string $url = null,
        public readonly ?string $type = null,
        public readonly ?string $caption = null,
        public readonly ?int $likes = null,
        public readonly ?int $comments = null,
        public readonly ?int $views = null,
        public readonly ?string $timestamp = null,
        public readonly ?string $thumbnailUrl = null,
        public readonly ?string $author = null,
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
            shortcode: isset($data['shortcode']) ? (string) $data['shortcode'] : null,
            url: isset($data['url']) ? (string) $data['url'] : null,
            type: isset($data['type']) ? (string) $data['type'] : null,
            caption: isset($data['caption']) ? (string) $data['caption'] : null,
            likes: isset($data['likes']) ? (int) $data['likes'] : null,
            comments: isset($data['comments']) ? (int) $data['comments'] : null,
            views: isset($data['views']) ? (int) $data['views'] : null,
            timestamp: isset($data['timestamp']) ? (string) $data['timestamp'] : null,
            thumbnailUrl: isset($data['thumbnail_url']) ? (string) $data['thumbnail_url'] : null,
            author: isset($data['author']) ? (string) $data['author'] : null,
            raw: $data,
        );
    }
}

/**
 * Represents Instagram channel posts response.
 */
final class ChannelPosts
{
    /**
     * @param list<InstagramPostItem> $items
     * @param array<string, mixed>    $raw
     */
    public function __construct(
        public readonly string $profileUrl,
        public readonly ?string $username = null,
        public readonly array $items = [],
        public readonly ?int $count = null,
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
        $items = [];
        foreach (($data['items'] ?? []) as $item) {
            if (is_array($item)) {
                $items[] = InstagramPostItem::fromArray($item);
            }
        }

        return new self(
            profileUrl: (string) ($data['profile_url'] ?? $data['url'] ?? ''),
            username: isset($data['username']) ? (string) $data['username'] : null,
            items: $items,
            count: isset($data['count']) ? (int) $data['count'] : null,
            hasMore: isset($data['has_more']) ? (bool) $data['has_more'] : null,
            cursor: isset($data['cursor']) ? (string) $data['cursor'] : null,
            raw: $data,
        );
    }
}
