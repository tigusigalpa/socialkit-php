<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a Twitter thread response.
 */
final class TwitterThread
{
    /**
     * @param list<TwitterTweet>    $tweets
     * @param array<string, mixed>  $author
     * @param array<string, mixed>  $raw
     */
    public function __construct(
        public readonly ?string $conversationId = null,
        public readonly ?int $tweetCount = null,
        public readonly ?bool $isThread = null,
        public readonly ?string $combinedText = null,
        public readonly array $author = [],
        public readonly array $tweets = [],
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
            conversationId: isset($data['conversation_id']) ? (string) $data['conversation_id'] : null,
            tweetCount: isset($data['tweet_count']) ? (int) $data['tweet_count'] : null,
            isThread: isset($data['is_thread']) ? (bool) $data['is_thread'] : null,
            combinedText: isset($data['combined_text']) ? (string) $data['combined_text'] : null,
            author: (array) ($data['author'] ?? []),
            tweets: $tweets,
            raw: $data,
        );
    }
}
