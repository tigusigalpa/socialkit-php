<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when the SocialKit API responds with HTTP 401 (Unauthorized),
 * typically indicating a missing, invalid, or revoked access key.
 */
final class AuthenticationException extends SocialKitException
{
    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        $message = (string) ($body['message'] ?? $body['error'] ?? 'Unauthorized: invalid or missing SocialKit access key.');

        return (new self($message, $statusCode))->withMeta($meta);
    }
}
