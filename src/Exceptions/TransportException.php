<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Throwable;
use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when a network-level error occurs before the API can respond
 * (e.g. DNS failure, connection refused, TLS error).
 */
final class TransportException extends SocialKitException
{
    public function __construct(string $message = 'SocialKit API transport error: unable to reach the server.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, 0, $previous);
    }

    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        return new self((string) ($body['message'] ?? 'Transport error'));
    }
}
