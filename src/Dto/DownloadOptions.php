<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Options for sync download endpoints (format, quality, country).
 */
final class DownloadOptions
{
    /** @var list<string> */
    private const FORMATS = ['mp4', 'mp3', 'avi', 'webm', 'm4a', 'ogg', 'wav'];

    /** @var list<string> */
    private const QUALITIES = ['240p', '360p', '480p', '720p', '1080p'];

    /**
     * @param string      $format  Output format: mp4, mp3, avi, webm, m4a, ogg, wav.
     * @param string      $quality Video quality: 240p, 360p, 480p, 720p, 1080p.
     * @param string|null $country Optional country code for geo-restricted content.
     *
     * @throws \InvalidArgumentException If format or quality is invalid.
     */
    public function __construct(
        public readonly string $format = 'mp4',
        public readonly string $quality = '720p',
        public readonly ?string $country = null,
    ) {
        if (!in_array($format, self::FORMATS, true)) {
            throw new \InvalidArgumentException('Invalid format. Allowed: ' . implode(', ', self::FORMATS));
        }

        if (!in_array($quality, self::QUALITIES, true)) {
            throw new \InvalidArgumentException('Invalid quality. Allowed: ' . implode(', ', self::QUALITIES));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $arr = [
            'format' => $this->format,
            'quality' => $this->quality,
        ];

        if ($this->country !== null) {
            $arr['country'] = $this->country;
        }

        return $arr;
    }
}
