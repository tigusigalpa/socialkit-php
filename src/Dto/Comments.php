<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a comments response with pagination support.
 */
final class Comments
{
    /**
     * @param list<Comment> $comments
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public readonly ?string $postUrl = null,
        public readonly ?string $videoId = null,
        public readonly array $comments = [],
        public readonly ?int $commentCount = null,
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
        $comments = [];
        foreach (($data['comments'] ?? []) as $comment) {
            if (is_array($comment)) {
                $comments[] = Comment::fromArray($comment);
            }
        }

        return new self(
            postUrl: isset($data['post_url']) ? (string) $data['post_url'] : null,
            videoId: isset($data['video_id']) ? (string) $data['video_id'] : null,
            comments: $comments,
            commentCount: isset($data['comment_count']) ? (int) $data['comment_count'] : null,
            hasMore: isset($data['has_more']) ? (bool) $data['has_more'] : null,
            cursor: isset($data['cursor']) ? (string) $data['cursor'] : null,
            raw: $data,
        );
    }
}
