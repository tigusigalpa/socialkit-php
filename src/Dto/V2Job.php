<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Represents a v2 async download job.
 *
 * States: queued → processing → ready | failed
 */
final class V2Job
{
    public function __construct(
        public readonly string $jobId,
        public readonly string $status,
        public readonly ?string $platform = null,
        public readonly ?string $url = null,
        public readonly ?string $quality = null,
        public readonly ?string $format = null,
        public readonly ?bool $billed = null,
        public readonly ?string $downloadUrl = null,
        public readonly ?int $expiresIn = null,
        public readonly ?int $durationSeconds = null,
        public readonly ?int $fileSize = null,
        public readonly ?float $fileSizeMB = null,
        public readonly ?float $creditsCost = null,
        public readonly ?string $title = null,
        public readonly ?string $thumbnail = null,
        public readonly ?string $error = null,
        public readonly ?string $errorCode = null,
        public readonly ?bool $retryable = null,
        public readonly array $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            jobId: (string) ($data['job_id'] ?? $data['jobId'] ?? ''),
            status: (string) ($data['status'] ?? 'queued'),
            platform: isset($data['platform']) ? (string) $data['platform'] : null,
            url: isset($data['url']) ? (string) $data['url'] : null,
            quality: isset($data['quality']) ? (string) $data['quality'] : null,
            format: isset($data['format']) ? (string) $data['format'] : null,
            billed: isset($data['billed']) ? (bool) $data['billed'] : null,
            downloadUrl: isset($data['download_url']) ? (string) $data['download_url'] : null,
            expiresIn: isset($data['expires_in']) ? (int) $data['expires_in'] : null,
            durationSeconds: isset($data['duration_seconds']) ? (int) $data['duration_seconds'] : null,
            fileSize: isset($data['file_size']) ? (int) $data['file_size'] : null,
            fileSizeMB: isset($data['file_size_mb']) ? (float) $data['file_size_mb'] : null,
            creditsCost: isset($data['credits_cost']) ? (float) $data['credits_cost'] : null,
            title: isset($data['title']) ? (string) $data['title'] : null,
            thumbnail: isset($data['thumbnail']) ? (string) $data['thumbnail'] : null,
            error: isset($data['error']) ? (string) $data['error'] : null,
            errorCode: isset($data['error_code']) ? (string) $data['error_code'] : null,
            retryable: isset($data['retryable']) ? (bool) $data['retryable'] : null,
            raw: $data,
        );
    }

    /**
     * Check if the job is in a terminal state (ready or failed).
     */
    public function isTerminal(): bool
    {
        return $this->status === 'ready' || $this->status === 'failed';
    }

    /**
     * Check if the job is ready.
     */
    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    /**
     * Check if the job has failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
