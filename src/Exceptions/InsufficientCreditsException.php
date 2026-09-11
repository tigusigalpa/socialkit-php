<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Exceptions;

use Tigusigalpa\SocialKit\ResponseMeta;

/**
 * Thrown when the SocialKit API responds with HTTP 403 and a
 * `code = insufficient_credits` field, indicating the account
 * does not have enough credits to fulfil the request.
 */
final class InsufficientCreditsException extends ForbiddenException
{
    /**
     * @param array<string, mixed> $body
     */
    public static function fromResponse(int $statusCode, array $body, ?ResponseMeta $meta = null): static
    {
        $message = (string) ($body['message'] ?? 'Insufficient credits to complete this request.');

        return new self(
            message: $message,
            httpStatus: $statusCode,
            requiredCredits: isset($body['required_credits']) ? (float) $body['required_credits'] : (isset($body['requiredCredits']) ? (float) $body['requiredCredits'] : null),
            remainingCredits: isset($body['remaining_credits']) ? (float) $body['remaining_credits'] : (isset($body['remainingCredits']) ? (float) $body['remainingCredits'] : null),
            shortfallCredits: isset($body['shortfall_credits']) ? (float) $body['shortfall_credits'] : (isset($body['shortfallCredits']) ? (float) $body['shortfallCredits'] : null),
            resetAt: isset($body['reset_at']) ? (string) $body['reset_at'] : (isset($body['resetAt']) ? (string) $body['resetAt'] : null),
            upgradeUrl: isset($body['upgrade_url']) ? (string) $body['upgrade_url'] : (isset($body['upgradeUrl']) ? (string) $body['upgradeUrl'] : null),
            topUpUrl: isset($body['top_up_url']) ? (string) $body['top_up_url'] : (isset($body['topUpUrl']) ? (string) $body['topUpUrl'] : null),
            incidentId: isset($body['incident_id']) ? (string) $body['incident_id'] : (isset($body['incidentId']) ? (string) $body['incidentId'] : null),
        );
    }
}
