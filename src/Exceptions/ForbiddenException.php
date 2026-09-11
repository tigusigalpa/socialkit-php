<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when the SocialKit API responds with HTTP 403 (Forbidden).
 *
 * May carry insufficient-credits metadata when the API returns a
 * `code = insufficient_credits` field.
 */
class ForbiddenException extends SocialKitException
{
    /**
     * @param float|null $requiredCredits   Credits required by the request.
     * @param float|null $remainingCredits  Credits remaining on the account.
     * @param float|null $shortfallCredits  Credits shortfall (required - remaining).
     * @param string|null $resetAt          ISO timestamp when credits reset.
     * @param string|null $upgradeUrl       URL to upgrade the plan.
     * @param string|null $topUpUrl         URL to top up credits.
     * @param string|null $incidentId       Incident identifier for support.
     */
    public function __construct(
        string $message,
        int $httpStatus = 403,
        public readonly ?float $requiredCredits = null,
        public readonly ?float $remainingCredits = null,
        public readonly ?float $shortfallCredits = null,
        public readonly ?string $resetAt = null,
        public readonly ?string $upgradeUrl = null,
        public readonly ?string $topUpUrl = null,
        public readonly ?string $incidentId = null,
    ) {
        parent::__construct($message, $httpStatus);
    }

    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        $message = (string) ($body['message'] ?? $body['error'] ?? 'Forbidden: access denied.');

        return (new self($message, $statusCode))->withMeta($meta);
    }
}
