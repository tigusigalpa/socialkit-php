<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for v2 async download start.
 *
 * @see https://docs.socialkit.dev/api-reference/async-download-api
 */
final class V2DownloadRequest
{
    /** @var list<string> */
    private const PLATFORMS = ['youtube', 'tiktok', 'instagram', 'facebook'];

    /** @var list<string> */
    private const FORMATS = ['mp4', 'mp3', 'avi', 'webm', 'm4a', 'ogg', 'wav'];

    /** @var list<string> */
    private const QUALITIES = ['240p', '360p', '480p', '720p', '1080p'];

    /**
     * @param string      $url         The video/post URL to download.
     * @param string      $format      Output format.
     * @param string      $quality     Video quality.
     * @param string|null $country     Optional country code.
     * @param int|null    $maxDuration Maximum duration in seconds (non-negative).
     * @param string      $platform    Target platform: youtube, tiktok, instagram, facebook.
     *
     * @throws \InvalidArgumentException If any value is invalid.
     */
    public function __construct(
        public readonly string $url,
        public readonly string $format = 'mp4',
        public readonly string $quality = '720p',
        public readonly ?string $country = null,
        public readonly ?int $maxDuration = null,
        public readonly string $platform = 'youtube',
    ) {
        if (!in_array($platform, self::PLATFORMS, true)) {
            throw new \InvalidArgumentException('Invalid platform. Allowed: ' . implode(', ', self::PLATFORMS));
        }

        if (!in_array($format, self::FORMATS, true)) {
            throw new \InvalidArgumentException('Invalid format. Allowed: ' . implode(', ', self::FORMATS));
        }

        if (!in_array($quality, self::QUALITIES, true)) {
            throw new \InvalidArgumentException('Invalid quality. Allowed: ' . implode(', ', self::QUALITIES));
        }

        if ($maxDuration !== null && $maxDuration < 0) {
            throw new \InvalidArgumentException('maxDuration must be non-negative.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $arr = [
            'url' => $this->url,
            'format' => $this->format,
            'quality' => $this->quality,
            'platform' => $this->platform,
        ];

        if ($this->country !== null) {
            $arr['country'] = $this->country;
        }

        if ($this->maxDuration !== null) {
            $arr['max_duration'] = $this->maxDuration;
        }

        return $arr;
    }

    /**
     * Get the API path for this platform's download endpoint.
     */
    public function getApiPath(): string
    {
        return '/v2/' . $this->platform . '/download';
    }
}
