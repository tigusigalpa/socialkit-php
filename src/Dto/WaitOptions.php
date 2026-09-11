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
    }
}
