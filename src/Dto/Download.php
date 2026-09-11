<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a sync download response.
 */
final class Download
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $title = null,
        public readonly ?string $duration = null,
        public readonly ?int $durationSeconds = null,
        public readonly ?string $downloadUrl = null,
        public readonly ?int $fileSize = null,
        public readonly ?float $fileSizeMB = null,
        public readonly ?string $format = null,
        public readonly ?string $quality = null,
        public readonly ?int $expiresIn = null,
        public readonly ?float $creditsUsed = null,
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
            title: isset($data['title']) ? (string) $data['title'] : null,
            duration: isset($data['duration']) ? (string) $data['duration'] : null,
            durationSeconds: isset($data['duration_seconds']) ? (int) $data['duration_seconds'] : null,
            downloadUrl: isset($data['download_url']) ? (string) $data['download_url'] : null,
            fileSize: isset($data['file_size']) ? (int) $data['file_size'] : null,
            fileSizeMB: isset($data['file_size_mb']) ? (float) $data['file_size_mb'] : null,
            format: isset($data['format']) ? (string) $data['format'] : null,
            quality: isset($data['quality']) ? (string) $data['quality'] : null,
            expiresIn: isset($data['expires_in']) ? (int) $data['expires_in'] : null,
            creditsUsed: isset($data['credits_used']) ? (float) $data['credits_used'] : null,
            raw: $data,
        );
    }
}
