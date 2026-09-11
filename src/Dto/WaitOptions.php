<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Options for the v2 download `wait()` polling method.
 */
final class WaitOptions
{
    /**
     * @param float      $interval        Initial polling interval in seconds.
     * @param float      $maxInterval     Maximum polling interval after backoff.
     * @param float|null $timeout         Overall timeout in seconds (null = no timeout).
     * @param int|null   $maxPolls        Maximum number of polls (null = unlimited, subject to timeout).
     */
    public function __construct(
        public readonly float $interval = 2.0,
        public readonly float $maxInterval = 10.0,
        public readonly ?float $timeout = 120.0,
        public readonly ?int $maxPolls = null,
    ) {
        if ($interval <= 0) {
            throw new \InvalidArgumentException('interval must be greater than zero.');
        }

        if ($maxInterval < $interval) {
            throw new \InvalidArgumentException('maxInterval must be greater than or equal to interval.');
        }

        if ($timeout !== null && $timeout <= 0) {
            throw new \InvalidArgumentException('timeout must be greater than zero when provided.');
        }

        if ($maxPolls !== null && $maxPolls <= 0) {
            throw new \InvalidArgumentException('maxPolls must be greater than zero when provided.');
        }
    }
}
