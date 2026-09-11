<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a LinkedIn article in a profile's recent articles.
 */
final class LinkedInArticle
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $url = null,
        public readonly ?string $date = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: isset($data['title']) ? (string) $data['title'] : null,
            url: isset($data['url']) ? (string) $data['url'] : null,
            date: isset($data['date']) ? (string) $data['date'] : null,
            raw: $data,
        );
    }
}

/**
 * Represents a LinkedIn profile.
 */
final class LinkedInProfile
{
    /**
     * @param list<LinkedInArticle> $recentArticles
     * @param array<string, mixed>  $raw
     */
    public function __construct(
        public readonly string $url,
        public readonly ?string $name = null,
        public readonly ?string $headline = null,
        public readonly ?int $followers = null,
        public readonly ?int $connections = null,
        public readonly ?string $profileImage = null,
        public readonly array $recentArticles = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $articles = [];
        foreach (($data['recent_articles'] ?? []) as $article) {
            if (is_array($article)) {
                $articles[] = LinkedInArticle::fromArray($article);
            }
        }

        return new self(
            url: (string) ($data['url'] ?? ''),
            name: isset($data['name']) ? (string) $data['name'] : null,
            headline: isset($data['headline']) ? (string) $data['headline'] : null,
            followers: isset($data['followers']) ? (int) $data['followers'] : null,
            connections: isset($data['connections']) ? (int) $data['connections'] : null,
            profileImage: isset($data['profile_image']) ? (string) $data['profile_image'] : null,
            recentArticles: $articles,
            raw: $data,
        );
    }
}
