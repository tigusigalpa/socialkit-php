<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when a v2 async download job has failed.
 *
 * Carries the upstream error message, error code, and a flag indicating
 * whether the job may be retried by submitting a new request.
 */
final class AsyncJobFailedException extends SocialKitException
{
    public function __construct(
        string $message,
        public readonly ?string $errorCode = null,
        public readonly bool $retryable = false,
    ) {
        parent::__construct($message, 0, 0);
    }

    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        $message = (string) ($body['error'] ?? $body['message'] ?? 'Async job failed.');
        $errorCode = isset($body['error_code']) ? (string) $body['error_code'] : null;
        $retryable = isset($body['retryable']) ? (bool) $body['retryable'] : false;

        return (new self($message, $errorCode, $retryable))->withMeta($meta);
    }
}
