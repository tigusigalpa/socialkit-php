<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents YouTube channel stats.
 */
final class YouTubeChannelStats
{
    public function __construct(
        public readonly string $profileUrl,
        public readonly ?string $username = null,
        public readonly ?string $nickname = null,
        public readonly ?string $bio = null,
        public readonly ?bool $verified = null,
        public readonly ?string $avatar = null,
        public readonly ?int $subscribers = null,
        public readonly ?int $totalVideos = null,
        public readonly ?string $bioLink = null,
        public readonly ?string $banner = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            profileUrl: (string) ($data['profile_url'] ?? $data['url'] ?? ''),
            username: isset($data['username']) ? (string) $data['username'] : null,
            nickname: isset($data['nickname']) ? (string) $data['nickname'] : null,
            bio: isset($data['bio']) ? (string) $data['bio'] : null,
            verified: isset($data['verified']) ? (bool) $data['verified'] : null,
            avatar: isset($data['avatar']) ? (string) $data['avatar'] : null,
            subscribers: isset($data['subscribers']) ? (int) $data['subscribers'] : null,
            totalVideos: isset($data['total_videos']) ? (int) $data['total_videos'] : null,
            bioLink: isset($data['bio_link']) ? (string) $data['bio_link'] : null,
            banner: isset($data['banner']) ? (string) $data['banner'] : null,
            raw: $data,
        );
    }
}
