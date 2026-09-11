<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when the SocialKit API responds with HTTP 429 (Too Many Requests)
 * and the configured number of automatic retries has been exhausted.
 */
final class RateLimitException extends SocialKitException
{
    public function __construct(
        string $message = 'SocialKit API rate limit exceeded.',
        int $httpStatus = 429,
        public readonly ?int $retryAfter = null,
    ) {
        parent::__construct($message, $httpStatus);
    }

    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        $message = (string) ($body['message'] ?? $body['error'] ?? 'SocialKit API rate limit exceeded.');
        $retryAfter = $meta?->retryAfter;

        return (new self($message, $statusCode, $retryAfter))->withMeta($meta);
    }
}
