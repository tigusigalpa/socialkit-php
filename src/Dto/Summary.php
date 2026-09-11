<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents an AI-generated summary response.
 */
final class Summary
{
    /**
     * @param list<string>          $mainTopics
     * @param list<string>          $keyPoints
     * @param list<string>          $quotes
     * @param array<string, mixed>  $extra  Fields from a custom_response schema.
     * @param array<string, mixed>  $raw
     */
    public function __construct(
        public readonly string $url,
        public readonly string $summary = '',
        public readonly array $mainTopics = [],
        public readonly array $keyPoints = [],
        public readonly ?string $tone = null,
        public readonly ?string $targetAudience = null,
        public readonly array $quotes = [],
        public readonly ?float $durationSeconds = null,
        public readonly ?float $creditsUsed = null,
        public readonly array $extra = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        // Known fields that are always mapped
        $knownKeys = ['url', 'summary', 'main_topics', 'key_points', 'tone',
            'target_audience', 'quotes', 'duration_seconds', 'credits_used'];

        // Extract extra fields (from custom_response)
        $extra = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, $knownKeys, true) && $key !== 'raw') {
                $extra[$key] = $value;
            }
        }

        return new self(
            url: (string) ($data['url'] ?? ''),
            summary: (string) ($data['summary'] ?? ''),
            mainTopics: array_values((array) ($data['main_topics'] ?? [])),
            keyPoints: array_values((array) ($data['key_points'] ?? [])),
            tone: isset($data['tone']) ? (string) $data['tone'] : null,
            targetAudience: isset($data['target_audience']) ? (string) $data['target_audience'] : null,
            quotes: array_values((array) ($data['quotes'] ?? [])),
            durationSeconds: isset($data['duration_seconds']) ? (float) $data['duration_seconds'] : null,
            creditsUsed: isset($data['credits_used']) ? (float) $data['credits_used'] : null,
            extra: $extra,
            raw: $data,
        );
    }
}
