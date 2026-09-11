<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a LinkedIn company.
 */
final class LinkedInCompany
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?int $followers = null,
        public readonly ?int $employees = null,
        public readonly ?string $logo = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: (string) ($data['url'] ?? ''),
            name: isset($data['name']) ? (string) $data['name'] : null,
            description: isset($data['description']) ? (string) $data['description'] : null,
            followers: isset($data['followers']) ? (int) $data['followers'] : null,
            employees: isset($data['employees']) ? (int) $data['employees'] : null,
            logo: isset($data['logo']) ? (string) $data['logo'] : null,
            raw: $data,
        );
    }
}
