<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when the SocialKit API responds with HTTP 404 (Not Found),
 * indicating the requested resource does not exist.
 */
final class NotFoundException extends SocialKitException
{
    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        $message = (string) ($body['message'] ?? $body['error'] ?? 'The requested SocialKit resource was not found.');

        return (new self($message, $statusCode))->withMeta($meta);
    }
}
