<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents the status of an individual API tool/service.
 */
final class StatusTool
{
    public function __construct(
        public readonly string $id,
        public readonly string $channel,
        public readonly string $name,
        public readonly string $status,
        public readonly ?string $message = null,
        public readonly ?string $updatedAt = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            channel: (string) ($data['channel'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            status: (string) ($data['status'] ?? 'unknown'),
            message: isset($data['message']) ? (string) $data['message'] : null,
            updatedAt: isset($data['updated_at']) ? (string) $data['updated_at'] : null,
            raw: $data,
        );
    }
}
