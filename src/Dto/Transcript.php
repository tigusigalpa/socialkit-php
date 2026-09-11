<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a video transcript response.
 */
final class Transcript
{
    /**
     * @param list<TranscriptSegment> $transcriptSegments
     * @param array<string, mixed>    $raw
     */
    public function __construct(
        public readonly string $url,
        public readonly ?string $videoId = null,
        public readonly string $transcript = '',
        public readonly array $transcriptSegments = [],
        public readonly ?int $wordCount = null,
        public readonly ?int $segments = null,
        public readonly ?string $language = null,
        public readonly ?float $durationSeconds = null,
        public readonly ?float $creditsUsed = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $segments = [];
        foreach (($data['transcript_segments'] ?? $data['segments'] ?? []) as $segment) {
            if (is_array($segment)) {
                $segments[] = TranscriptSegment::fromArray($segment);
            }
        }

        return new self(
            url: (string) ($data['url'] ?? ''),
            videoId: isset($data['video_id']) ? (string) $data['video_id'] : null,
            transcript: (string) ($data['transcript'] ?? ''),
            transcriptSegments: $segments,
            wordCount: isset($data['word_count']) ? (int) $data['word_count'] : null,
            segments: isset($data['segments']) ? (int) $data['segments'] : null,
            language: isset($data['language']) ? (string) $data['language'] : null,
            durationSeconds: isset($data['duration_seconds']) ? (float) $data['duration_seconds'] : null,
            creditsUsed: isset($data['credits_used']) ? (float) $data['credits_used'] : null,
            raw: $data,
        );
    }
}
