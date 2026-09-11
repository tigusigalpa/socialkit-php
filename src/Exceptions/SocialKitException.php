<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use RuntimeException;
use Throwable;
use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Abstract base exception for all errors raised by the SocialKit SDK.
 *
 * Every exception thrown by this package extends this class, allowing
 * consumers to catch a single type to handle any SDK-related failure.
 */
abstract class SocialKitException extends RuntimeException
{
    protected ?ResponseMeta $meta = null;

    /**
     * @param string         $message    Human-readable error message.
     * @param int            $httpStatus HTTP status code returned by the API.
     * @param int            $code       Internal SDK error code.
     * @param Throwable|null $previous   Previous exception used for chaining.
     */
    public function __construct(
        string $message,
        public readonly int $httpStatus = 0,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the response metadata, if available.
     */
    public function getMeta(): ?ResponseMeta
    {
        return $this->meta;
    }

    /**
     * Set the response metadata.
     */
    public function withMeta(?ResponseMeta $meta): static
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Build an instance from an HTTP status code and a decoded response body.
     *
     * @param array<string, mixed> $body
     */
    abstract public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static;
}
