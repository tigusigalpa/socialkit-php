<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a credit breakdown (monthly or purchased).
 */
final class CreditsBreakdown
{
    public function __construct(
        public readonly ?float $remaining = null,
        public readonly ?float $used = null,
        public readonly ?float $total = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            remaining: isset($data['remaining']) ? (float) $data['remaining'] : null,
            used: isset($data['used']) ? (float) $data['used'] : null,
            total: isset($data['total']) ? (float) $data['total'] : null,
            raw: $data,
        );
    }
}
