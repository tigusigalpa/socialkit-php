<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents Instagram channel stats.
 */
final class InstagramChannelStats
{
    public function __construct(
        public readonly string $profileUrl,
        public readonly ?string $userId = null,
        public readonly ?string $username = null,
        public readonly ?string $fullName = null,
        public readonly ?bool $verified = null,
        public readonly ?int $followers = null,
        public readonly ?int $following = null,
        public readonly ?int $totalPosts = null,
        public readonly ?string $bio = null,
        public readonly ?string $avatar = null,
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
            userId: isset($data['user_id']) ? (string) $data['user_id'] : null,
            username: isset($data['username']) ? (string) $data['username'] : null,
            fullName: isset($data['full_name']) ? (string) $data['full_name'] : null,
            verified: isset($data['verified']) ? (bool) $data['verified'] : null,
            followers: isset($data['followers']) ? (int) $data['followers'] : null,
            following: isset($data['following']) ? (int) $data['following'] : null,
            totalPosts: isset($data['total_posts']) ? (int) $data['total_posts'] : null,
            bio: isset($data['bio']) ? (string) $data['bio'] : null,
            avatar: isset($data['avatar']) ? (string) $data['avatar'] : null,
            raw: $data,
        );
    }
}
