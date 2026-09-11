<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a LinkedIn company posts response.
 */
final class LinkedInCompanyPosts
{
    /**
     * @param list<LinkedInPost>    $posts
     * @param array<string, mixed>  $raw
     */
    public function __construct(
        public readonly array $posts = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $posts = [];
        foreach (($data['posts'] ?? []) as $post) {
            if (is_array($post)) {
                $posts[] = LinkedInPost::fromArray($post);
            }
        }

        return new self(
            posts: $posts,
            raw: $data,
        );
    }
}
