<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a single comment.
 *
 * Common fields are typed; platform-specific extras (author, avatar, dates, etc.)
 * are kept in the `raw` property for forward compatibility.
 */
final class Comment
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $text = null,
        public readonly ?int $likeCount = null,
        public readonly ?int $replyCount = null,
        public readonly ?string $author = null,
        public readonly ?string $username = null,
        public readonly ?int $likes = null,
        public readonly ?string $date = null,
        public readonly ?string $avatar = null,
        public readonly ?int $position = null,
        public readonly ?string $createTime = null,
        public readonly array $user = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (string) $data['id'] : null,
            text: isset($data['text']) ? (string) $data['text'] : null,
            likeCount: isset($data['like_count']) ? (int) $data['like_count'] : null,
            replyCount: isset($data['reply_count']) ? (int) $data['reply_count'] : null,
            author: isset($data['author']) ? (string) $data['author'] : null,
            username: isset($data['username']) ? (string) $data['username'] : null,
            likes: isset($data['likes']) ? (int) $data['likes'] : null,
            date: isset($data['date']) ? (string) $data['date'] : null,
            avatar: isset($data['avatar']) ? (string) $data['avatar'] : null,
            position: isset($data['position']) ? (int) $data['position'] : null,
            createTime: isset($data['create_time']) ? (string) $data['create_time'] : null,
            user: (array) ($data['user'] ?? []),
            raw: $data,
        );
    }
}
