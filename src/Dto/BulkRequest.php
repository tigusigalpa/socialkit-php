<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for bulk (experimental) endpoints.
 *
 * Each entry in the `requests` array should be a JSON-safe associative array
 * representing a single request that would normally be sent individually.
 *
 * @experimental
 */
final class BulkRequest
{
    /**
     * @param list<array<string, mixed>> $requests
     */
    public function __construct(
        public readonly array $requests,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'requests' => $this->requests,
        ];
    }
}
