<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a Twitter/X profile.
 */
final class TwitterProfile
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $username = null,
        public readonly ?string $bio = null,
        public readonly ?int $followers = null,
        public readonly ?int $following = null,
        public readonly ?int $tweets = null,
        public readonly ?bool $verified = null,
        public readonly ?string $profileImage = null,
        public readonly ?string $bannerImage = null,
        public readonly ?string $location = null,
        public readonly ?string $website = null,
        public readonly ?string $joinedAt = null,
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
            id: isset($data['id']) ? (string) $data['id'] : null,
            name: isset($data['name']) ? (string) $data['name'] : null,
            username: isset($data['username']) ? (string) $data['username'] : null,
            bio: isset($data['bio']) ? (string) $data['bio'] : null,
            followers: isset($data['followers']) ? (int) $data['followers'] : null,
            following: isset($data['following']) ? (int) $data['following'] : null,
            tweets: isset($data['tweets']) ? (int) $data['tweets'] : null,
            verified: isset($data['verified']) ? (bool) $data['verified'] : null,
            profileImage: isset($data['profile_image']) ? (string) $data['profile_image'] : null,
            bannerImage: isset($data['banner_image']) ? (string) $data['banner_image'] : null,
            location: isset($data['location']) ? (string) $data['location'] : null,
            website: isset($data['website']) ? (string) $data['website'] : null,
            joinedAt: isset($data['joined_at']) ? (string) $data['joined_at'] : null,
            raw: $data,
        );
    }
}
