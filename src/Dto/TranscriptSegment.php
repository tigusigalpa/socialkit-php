<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a single segment of a transcript.
 */
final class TranscriptSegment
{
    public function __construct(
        public readonly string $text,
        public readonly float $start,
        public readonly float $duration,
        public readonly ?string $timestamp = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            text: (string) ($data['text'] ?? ''),
            start: (float) ($data['start'] ?? 0),
            duration: (float) ($data['duration'] ?? 0),
            timestamp: isset($data['timestamp']) ? (string) $data['timestamp'] : null,
            raw: $data,
        );
    }
}
