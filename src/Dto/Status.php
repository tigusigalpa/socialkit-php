<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents the overall API service status.
 */
final class Status
{
    /**
     * @param string             $overall     Overall status: green, yellow, or red.
     * @param string             $generatedAt ISO timestamp of status generation.
     * @param list<StatusTool>   $tools       Status of individual API tools.
     * @param array<string, mixed> $raw       Original payload.
     */
    public function __construct(
        public readonly string $overall,
        public readonly string $generatedAt,
        public readonly array $tools,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $tools = [];
        foreach (($data['tools'] ?? []) as $tool) {
            if (is_array($tool)) {
                $tools[] = StatusTool::fromArray($tool);
            }
        }

        return new self(
            overall: (string) ($data['overall'] ?? 'unknown'),
            generatedAt: (string) ($data['generated_at'] ?? ''),
            tools: $tools,
            raw: $data,
        );
    }
}
