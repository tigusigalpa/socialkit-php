<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Throwable;
use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when an HTTP request times out before the API responds.
 */
final class TimeoutException extends SocialKitException
{
    public function __construct(string $message = 'SocialKit API request timed out.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, 0, $previous);
    }

    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        return new self((string) ($body['message'] ?? 'Request timed out'));
    }
}
