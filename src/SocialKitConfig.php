<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit;

/**
 * Immutable configuration object for the SocialKit SDK.
 *
 * Holds the API access key, base URL, and HTTP/retry tuning parameters used by
 * {@see SocialKitClient} to build and send requests.
 */
final class SocialKitConfig
{
    /**
     * @param string               $accessKey      SocialKit API access key.
     * @param string               $baseUrl        Base URL of the SocialKit API (no trailing slash).
     * @param float                $timeout        Request timeout in seconds.
     * @param int                  $retryAttempts  Number of automatic retries on transient errors (0 = no retries).
     * @param float                $retryDelay     Base delay (in seconds) for exponential backoff between retries.
     * @param string               $userAgent      User-Agent header value sent with every request.
     * @param array<string, mixed> $defaultHeaders Additional default headers merged into every request.
     * @param bool                 $keyInQuery     Compatibility opt-in: additionally send the access key as a
     *                                            query/body parameter.
     */
    public function __construct(
        public readonly string $accessKey,
        public readonly string $baseUrl = 'https://api.socialkit.dev',
        public readonly float $timeout = 30.0,
        public readonly int $retryAttempts = 0,
        public readonly float $retryDelay = 1.0,
        public readonly string $userAgent = 'SocialKit-PHP-SDK/1.0.0',
        public readonly array $defaultHeaders = [],
        public readonly bool $keyInQuery = false,
    ) {
    }

    /**
     * Create a configuration instance from a plain associative array.
     *
     * Recognized keys: `access_key`, `base_url`, `timeout`, `retry_attempts`,
     * `retry_delay`, `user_agent`, `default_headers`, `key_in_query`.
     *
     * @param array<string, mixed> $config
     */
    public static function fromArray(array $config): self
    {
        return new self(
            accessKey: (string) ($config['access_key'] ?? ''),
            baseUrl: rtrim((string) ($config['base_url'] ?? 'https://api.socialkit.dev'), '/'),
            timeout: (float) ($config['timeout'] ?? 30.0),
            retryAttempts: (int) ($config['retry_attempts'] ?? 0),
            retryDelay: (float) ($config['retry_delay'] ?? 1.0),
            userAgent: (string) ($config['user_agent'] ?? 'SocialKit-PHP-SDK/1.0.0'),
            defaultHeaders: (array) ($config['default_headers'] ?? []),
            keyInQuery: self::toBool($config['key_in_query'] ?? false),
        );
    }

    /**
     * Create a configuration instance from environment variables.
     *
     * Recognized variables: `SOCIALKIT_ACCESS_KEY`, `SOCIALKIT_BASE_URL`,
     * `SOCIALKIT_TIMEOUT`, `SOCIALKIT_RETRY_ATTEMPTS`, `SOCIALKIT_RETRY_DELAY`,
     * `SOCIALKIT_USER_AGENT`, `SOCIALKIT_KEY_IN_QUERY`.
     */
    public static function fromEnv(): self
    {
        return self::fromArray([
            'access_key' => getenv('SOCIALKIT_ACCESS_KEY') ?: '',
            'base_url' => getenv('SOCIALKIT_BASE_URL') ?: 'https://api.socialkit.dev',
            'timeout' => getenv('SOCIALKIT_TIMEOUT') ?: 30.0,
            'retry_attempts' => getenv('SOCIALKIT_RETRY_ATTEMPTS') ?: 0,
            'retry_delay' => getenv('SOCIALKIT_RETRY_DELAY') ?: 1.0,
            'user_agent' => getenv('SOCIALKIT_USER_AGENT') ?: 'SocialKit-PHP-SDK/1.0.0',
            'key_in_query' => getenv('SOCIALKIT_KEY_IN_QUERY') ?: false,
        ]);
    }

    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value !== 0;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
        }

        return false;
    }
}
