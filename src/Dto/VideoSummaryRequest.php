<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Request DTO for video summary endpoint (direct video file URL).
 *
 * @see https://docs.socialkit.dev/api-reference/video-summarize-api
 */
final class VideoSummaryRequest
{
    public function __construct(
        public readonly string $url,
        public readonly ?string $customPrompt = null,
        public readonly ?string $customResponse = null,
        public readonly bool $cache = false,
        public readonly int $cacheTtl = 2_592_000,
    ) {
        if ($cacheTtl < 3600 || $cacheTtl > 2_592_000) {
            throw new \InvalidArgumentException('cacheTtl must be between 3600 and 2592000 seconds.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $arr = [
            'url' => $this->url,
            'cache' => $this->cache,
            'cache_ttl' => $this->cacheTtl,
        ];

        if ($this->customPrompt !== null) {
            $arr['custom_prompt'] = $this->customPrompt;
        }

        if ($this->customResponse !== null) {
            $decoded = json_decode($this->customResponse, true);
            $arr['custom_response'] = $decoded !== null ? $decoded : $this->customResponse;
        }

        return $arr;
    }
}
