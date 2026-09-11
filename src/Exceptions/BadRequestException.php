<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when the SocialKit API responds with HTTP 400 (Bad Request),
 * indicating a malformed or invalid request payload.
 */
final class BadRequestException extends SocialKitException
{
    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        $message = (string) ($body['message'] ?? $body['error'] ?? 'Bad request: the request was malformed or invalid.');

        return (new self($message, $statusCode))->withMeta($meta);
    }
}
