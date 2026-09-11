<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit;

/**
 * Generic typed response wrapper for all SocialKit API responses.
 *
 * @template T
 */
final class ApiResponse
{
    /**
     * @param T            $data    The hydrated response data (DTO, array, or scalar).
     * @param ResponseMeta $meta    Metadata extracted from the HTTP response headers.
     * @param bool         $success Whether the API returned a success indicator.
     */
    public function __construct(
        public readonly mixed $data,
        public readonly ResponseMeta $meta,
        public readonly bool $success,
    ) {
    }
}
