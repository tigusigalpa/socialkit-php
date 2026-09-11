<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Laravel;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\WaitOptions;
use Tigusigalpa\SocialKit\ResponseMeta;
use Tigusigalpa\SocialKit\SocialKitClientInterface;
use Tigusigalpa\SocialKit\Services\DownloadsService;
use Tigusigalpa\SocialKit\Services\FacebookService;
use Tigusigalpa\SocialKit\Services\InstagramService;
use Tigusigalpa\SocialKit\Services\LinkedInService;
use Tigusigalpa\SocialKit\Services\ServiceService;
use Tigusigalpa\SocialKit\Services\TikTokService;
use Tigusigalpa\SocialKit\Services\TwitterService;
use Tigusigalpa\SocialKit\Services\VideoService;
use Tigusigalpa\SocialKit\Services\YouTubeService;

/**
 * Test fake implementing {@see SocialKitClientInterface}.
 *
 * Allows queueing fixture responses and asserting requests without
 * touching the network. Intended for use in test suites via
 * `SocialKit::fake()`.
 */
final class SocialKitFake implements SocialKitClientInterface
{
    /** @var list<ApiResponse> */
    private array $queue = [];

    /** @var list<array{path: string, body: ?array, method: string, query: array}> */
    private array $recordedRequests = [];

    /**
     * Queue a response to be returned by the next request() call.
     *
     * @param ApiResponse $response
     */
    public function queueResponse(ApiResponse $response): self
    {
        $this->queue[] = $response;

        return $this;
    }

    /**
     * Queue a simple data response with default meta.
     *
     * @param mixed $data
     */
    public function queueData(mixed $data, bool $success = true): self
    {
        $meta = new ResponseMeta(200, [], null, null, null, null, null, null, '');

        return $this->queueResponse(new ApiResponse($data, $meta, $success));
    }

    /**
     * Get all recorded requests.
     *
     * @return list<array{path: string, body: ?array, method: string, query: array}>
     */
    public function recordedRequests(): array
    {
        return $this->recordedRequests;
    }

    /**
     * Assert that a request was made to the given path.
     */
    public function assertRequested(string $path): void
    {
        foreach ($this->recordedRequests as $request) {
            if ($request['path'] === $path) {
                return;
            }
        }

        throw new \PHPUnit\Framework\AssertionFailedError("No request was made to path: {$path}");
    }

    /**
     * Assert the total number of requests made.
     */
    public function assertRequestCount(int $count): void
    {
        $actual = count($this->recordedRequests);

        if ($actual !== $count) {
            throw new \PHPUnit\Framework\AssertionFailedError("Expected {$count} requests, but {$actual} were made.");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function request(
        string $path,
        ?array $body = null,
        string $method = 'POST',
        array $query = [],
    ): ApiResponse {
        $this->recordedRequests[] = [
            'path' => $path,
            'body' => $body,
            'method' => $method,
            'query' => $query,
        ];

        if ($this->queue === []) {
            $meta = new ResponseMeta(200, [], null, null, null, null, null, null, '');

            return new ApiResponse([], $meta, true);
        }

        return array_shift($this->queue);
    }

    public function status(): ServiceService
    {
        return new ServiceService($this);
    }

    public function youtube(): YouTubeService
    {
        return new YouTubeService($this);
    }

    public function tiktok(): TikTokService
    {
        return new TikTokService($this);
    }

    public function instagram(): InstagramService
    {
        return new InstagramService($this);
    }

    public function facebook(): FacebookService
    {
        return new FacebookService($this);
    }

    public function twitter(): TwitterService
    {
        return new TwitterService($this);
    }

    public function linkedin(): LinkedInService
    {
        return new LinkedInService($this);
    }

    public function video(): VideoService
    {
        return new VideoService($this);
    }

    public function downloads(): DownloadsService
    {
        return new DownloadsService($this);
    }
}
