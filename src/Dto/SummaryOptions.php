<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Dto;

/**
 * Options for AI summary endpoints, extending {@see FetchOptions} with
 * custom prompt and custom response schema support.
 */
final class SummaryOptions
{
    public function __construct(
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
            'cache' => $this->cache,
            'cache_ttl' => $this->cacheTtl,
        ];

        if ($this->customPrompt !== null) {
            $arr['custom_prompt'] = $this->customPrompt;
        }

        if ($this->customResponse !== null) {
            // custom_response can be a JSON object string; decode if valid, otherwise pass as string
            $decoded = json_decode($this->customResponse, true);
            $arr['custom_response'] = $decoded !== null ? $decoded : $this->customResponse;
        }

        return $arr;
    }
}
