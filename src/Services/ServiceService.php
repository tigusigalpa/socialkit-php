<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\Credits;
use Tigusigalpa\SocialKit\Dto\Status;

/**
 * Service for the SocialKit status and credits endpoints.
 *
 * @see https://docs.socialkit.dev/api-reference/status-api
 * @see https://docs.socialkit.dev/api-reference/credits-api
 */
final class ServiceService extends AbstractService
{
    /**
     * Fetch the overall API service status.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/status-api
     *
     * @return ApiResponse<Status>
     */
    public function status(): ApiResponse
    {
        return $this->getRequest('/status', [], Status::class);
    }

    /**
     * Fetch the account's credit balance.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/credits-api
     *
     * @return ApiResponse<Credits>
     */
    public function credits(): ApiResponse
    {
        return $this->getRequest('/credits', [], Credits::class);
    }
}
