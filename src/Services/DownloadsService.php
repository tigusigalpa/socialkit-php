<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\V2DownloadRequest;
use Tigusigalpa\SocialKit\Dto\V2Job;
use Tigusigalpa\SocialKit\Dto\WaitOptions;
use Tigusigalpa\SocialKit\Exceptions\AsyncJobFailedException;

/**
 * Service for the SocialKit v2 async download endpoints.
 *
 * @see https://docs.socialkit.dev/api-reference/async-download-api
 */
final class DownloadsService extends AbstractService
{
    /**
     * Start a v2 async download job.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/async-download-api
     *
     * @param V2DownloadRequest $req
     *
     * @return ApiResponse<V2Job>
     */
    public function start(V2DownloadRequest $req): ApiResponse
    {
        return $this->postRequest($req->getApiPath(), $req->toArray(), V2Job::class);
    }

    /**
     * Get the status of a v2 async download job (ALWAYS GET).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/async-download-api
     *
     * @param string $jobId
     *
     * @return ApiResponse<V2Job>
     */
    public function get(string $jobId): ApiResponse
    {
        if ($jobId === '') {
            throw new \InvalidArgumentException('jobId must not be empty.');
        }

        return $this->getRequest('/v2/downloads/' . rawurlencode($jobId), [], V2Job::class);
    }

    /**
     * Poll a v2 async download job until it reaches a terminal state (ready or failed).
     *
     * Uses caller-controlled interval and backoff, respects context timeout,
     * and never polls infinitely. No auto-resubmit on failure.
     *
     * @param string       $jobId
     * @param WaitOptions  $opts
     *
     * @return ApiResponse<V2Job>
     *
     * @throws AsyncJobFailedException If the job fails.
     * @throws \Tigusigalpa\SocialKit\Exceptions\SocialKitException If the timeout is exceeded.
     */
    public function wait(string $jobId, ?WaitOptions $opts = null): ApiResponse
    {
        $opts ??= new WaitOptions();

        $startTime = microtime(true);
        $interval = $opts->interval;
        $pollCount = 0;

        while (true) {
            $response = $this->get($jobId);
            /** @var V2Job $job */
            $job = $response->data;

            if ($job->isReady()) {
                return $response;
            }

            if ($job->isFailed()) {
                throw AsyncJobFailedException::fromResponse(0, [
                    'error' => $job->error ?? 'Job failed.',
                    'error_code' => $job->errorCode,
                    'retryable' => $job->retryable ?? false,
                ], $response->meta);
            }

            $pollCount++;

            // Check max polls
            if ($opts->maxPolls !== null && $pollCount >= $opts->maxPolls) {
                return $response;
            }

            // Check timeout
            if ($opts->timeout !== null) {
                $elapsed = microtime(true) - $startTime;
                if ($elapsed + $interval > $opts->timeout) {
                    return $response;
                }
            }

            // Sleep before next poll
            usleep((int) ($interval * 1_000_000));

            // Exponential backoff with cap
            $interval = min($interval * 1.5, $opts->maxInterval);
        }
    }
}
