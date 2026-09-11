<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents TikTok channel stats.
 */
final class TikTokChannelStats
{
    public function __construct(
        public readonly string $profileUrl,
        public readonly ?string $username = null,
        public readonly ?string $nickname = null,
        public readonly ?string $signature = null,
        public readonly ?bool $verified = null,
        public readonly ?string $avatar = null,
        public readonly ?int $followers = null,
        public readonly ?int $following = null,
        public readonly ?int $totalLikes = null,
        public readonly ?int $totalVideos = null,
        public readonly ?string $bioLink = null,
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
            signature: isset($data['signature']) ? (string) $data['signature'] : null,
            verified: isset($data['verified']) ? (bool) $data['verified'] : null,
            avatar: isset($data['avatar']) ? (string) $data['avatar'] : null,
            followers: isset($data['followers']) ? (int) $data['followers'] : null,
            following: isset($data['following']) ? (int) $data['following'] : null,
            totalLikes: isset($data['total_likes']) ? (int) $data['total_likes'] : null,
            totalVideos: isset($data['total_videos']) ? (int) $data['total_videos'] : null,
            bioLink: isset($data['bio_link']) ? (string) $data['bio_link'] : null,
            raw: $data,
        );
    }
}
