<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents the account's credit balance.
 */
final class Credits
{
    public function __construct(
        public readonly float $totalRemaining,
        public readonly ?CreditsBreakdown $monthly = null,
        public readonly ?CreditsBreakdown $purchased = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $monthly = isset($data['monthly']) && is_array($data['monthly'])
            ? CreditsBreakdown::fromArray($data['monthly'])
            : null;
        $purchased = isset($data['purchased']) && is_array($data['purchased'])
            ? CreditsBreakdown::fromArray($data['purchased'])
            : null;

        return new self(
            totalRemaining: (float) ($data['total_remaining'] ?? $data['totalRemaining'] ?? 0),
            monthly: $monthly,
            purchased: $purchased,
            raw: $data,
        );
    }
}
