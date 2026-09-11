<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for sync download endpoints (YouTube, TikTok, Instagram).
 *
 * @see https://docs.socialkit.dev/api-reference/youtube-download-api
 */
final class DownloadRequest
{
    public function __construct(
        public readonly string $url,
        public readonly string $format = 'mp4',
        public readonly string $quality = '720p',
        public readonly ?string $country = null,
    ) {
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
        ];

        if ($this->country !== null) {
            $arr['country'] = $this->country;
        }

        return $arr;
    }
}
