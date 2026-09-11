<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Throwable;
use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when the API response body cannot be decoded as valid JSON.
 */
final class DecodeException extends SocialKitException
{
    public function __construct(string $message = 'Failed to decode SocialKit API response: invalid JSON.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, 0, $previous);
    }

    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        return new self((string) ($body['message'] ?? 'JSON decode error'));
    }
}
