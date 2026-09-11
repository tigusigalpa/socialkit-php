<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\SocialKitClientInterface;

/**
 * Base class for all SocialKit service groups.
 *
 * Provides the shared request dispatch and response hydration logic
 * used by every platform-specific service.
 */
abstract class AbstractService
{
    public function __construct(protected readonly SocialKitClientInterface $client)
    {
    }

    /**
     * Send a POST request with a JSON body and hydrate the response into a DTO.
     *
     * @param string                    $path     API path.
     * @param array<string, mixed>|null $body     JSON body array.
     * @param class-string              $dtoClass DTO class to hydrate.
     *
     * @return ApiResponse<mixed>
     */
    protected function postRequest(string $path, ?array $body, string $dtoClass): ApiResponse
    {
        $response = $this->client->request($path, $body, 'POST');

        return $this->hydrate($response, $dtoClass);
    }

    /**
     * Send a GET request with query params and hydrate the response into a DTO.
     *
     * @param string               $path     API path.
     * @param array<string, mixed> $query    Query parameters.
     * @param class-string         $dtoClass DTO class to hydrate.
     *
     * @return ApiResponse<mixed>
     */
    protected function getRequest(string $path, array $query, string $dtoClass): ApiResponse
    {
        $response = $this->client->request($path, null, 'GET', $query);

        return $this->hydrate($response, $dtoClass);
    }

    /**
     * Send a POST request and return the raw ApiResponse without hydration.
     *
     * @param string                    $path API path.
     * @param array<string, mixed>|null $body JSON body array.
     *
     * @return ApiResponse<mixed>
     */
    protected function postRawRequest(string $path, ?array $body): ApiResponse
    {
        return $this->client->request($path, $body, 'POST');
    }

    /**
     * Hydrate an ApiResponse's data into a DTO instance.
     *
     * @param ApiResponse<mixed> $response
     * @param class-string       $dtoClass
     *
     * @return ApiResponse<mixed>
     */
    protected function hydrate(ApiResponse $response, string $dtoClass): ApiResponse
    {
        $data = $response->data;

        if (is_array($data)) {
            $dto = $dtoClass::fromArray($data);
        } else {
            $dto = $data;
        }

        return new ApiResponse($dto, $response->meta, $response->success);
    }
}
