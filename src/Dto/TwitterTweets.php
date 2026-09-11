<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a Twitter tweets response with pagination.
 */
final class TwitterTweets
{
    /**
     * @param list<TwitterTweet>    $tweets
     * @param array<string, mixed>  $raw
     */
    public function __construct(
        public readonly array $tweets = [],
        public readonly ?string $nextCursor = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $tweets = [];
        foreach (($data['tweets'] ?? []) as $tweet) {
            if (is_array($tweet)) {
                $tweets[] = TwitterTweet::fromArray($tweet);
            }
        }

        return new self(
            tweets: $tweets,
            nextCursor: isset($data['next_cursor']) ? (string) $data['next_cursor'] : null,
            raw: $data,
        );
    }
}
