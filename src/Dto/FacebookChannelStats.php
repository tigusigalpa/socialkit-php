<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents Facebook channel/page stats.
 */
final class FacebookChannelStats
{
    public function __construct(
        public readonly string $profileUrl,
        public readonly ?string $id = null,
        public readonly ?string $fullName = null,
        public readonly ?string $bio = null,
        public readonly ?string $avatar = null,
        public readonly ?string $coverPhoto = null,
        public readonly ?bool $verified = null,
        public readonly ?int $followers = null,
        public readonly ?string $category = null,
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
            id: isset($data['id']) ? (string) $data['id'] : null,
            fullName: isset($data['full_name']) ? (string) $data['full_name'] : null,
            bio: isset($data['bio']) ? (string) $data['bio'] : null,
            avatar: isset($data['avatar']) ? (string) $data['avatar'] : null,
            coverPhoto: isset($data['cover_photo']) ? (string) $data['cover_photo'] : null,
            verified: isset($data['verified']) ? (bool) $data['verified'] : null,
            followers: isset($data['followers']) ? (int) $data['followers'] : null,
            category: isset($data['category']) ? (string) $data['category'] : null,
            raw: $data,
        );
    }
}
