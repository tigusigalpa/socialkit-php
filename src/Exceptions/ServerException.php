<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when the SocialKit API responds with HTTP 5xx (Server Error),
 * indicating an upstream service issue.
 */
final class ServerException extends SocialKitException
{
    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        $message = (string) ($body['message'] ?? $body['error'] ?? "SocialKit API server error (HTTP {$statusCode}).");

        return (new self($message, $statusCode))->withMeta($meta);
    }
}
