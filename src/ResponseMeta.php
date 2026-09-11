<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit;

/**
 * Immutable metadata extracted from an API response's HTTP headers.
 *
 * Captures credit usage, rate-limit, and retry information so callers can
 * inspect them without parsing raw headers. The raw body is stored in a
 * redacted form to ensure the API key is never exposed.
 */
final class ResponseMeta
{
    /**
     * @param int                       $httpStatus         HTTP status code of the response.
     * @param array<string, list<string>> $headers          All response headers.
     * @param float|null                $creditsUsed        Credits consumed by this request (X-Credits-Used).
     * @param float|null                $creditsRemaining   Credits remaining after this request (X-Credits-Remaining).
     * @param int|null                  $retryAfter         Seconds to wait before retrying (Retry-After).
     * @param int|null                  $rateLimitLimit     Rate limit ceiling (X-RateLimit-Limit).
     * @param int|null                  $rateLimitRemaining Remaining requests in window (X-RateLimit-Remaining).
     * @param int|null                  $rateLimitReset     Unix timestamp when the rate-limit window resets (X-RateLimit-Reset).
     * @param string                    $rawBody            The response body with any API key redacted.
     */
    public function __construct(
        public readonly int $httpStatus,
        public readonly array $headers,
        public readonly ?float $creditsUsed,
        public readonly ?float $creditsRemaining,
        public readonly ?int $retryAfter,
        public readonly ?int $rateLimitLimit,
        public readonly ?int $rateLimitRemaining,
        public readonly ?int $rateLimitReset,
        public readonly string $rawBody,
    ) {
    }

    /**
     * Build a ResponseMeta from a PSR-7 response and a redacted raw body.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string                              $redactedBody
     */
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, string $redactedBody): self
    {
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[$name] = $values;
        }

        return new self(
            httpStatus: $response->getStatusCode(),
            headers: $headers,
            creditsUsed: self::parseOptionalFloat($response->getHeaderLine('X-Credits-Used')),
            creditsRemaining: self::parseOptionalFloat($response->getHeaderLine('X-Credits-Remaining')),
            retryAfter: self::parseOptionalInt($response->getHeaderLine('Retry-After')),
            rateLimitLimit: self::parseOptionalInt($response->getHeaderLine('X-RateLimit-Limit')),
            rateLimitRemaining: self::parseOptionalInt($response->getHeaderLine('X-RateLimit-Remaining')),
            rateLimitReset: self::parseOptionalInt($response->getHeaderLine('X-RateLimit-Reset')),
            rawBody: $redactedBody,
        );
    }

    private static function parseOptionalFloat(string $value): ?float
    {
        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private static function parseOptionalInt(string $value): ?int
    {
        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }
}
