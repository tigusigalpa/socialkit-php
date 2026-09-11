<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Dto\VideoSummaryRequest;
use Tigusigalpa\SocialKit\Dto\VideoTranscriptRequest;

/**
 * Service for the SocialKit Video endpoints (direct video file URLs).
 *
 * @see https://docs.socialkit.dev/video
 */
final class VideoService extends AbstractService
{
    /**
     * Fetch a transcript from a direct video file URL (5 credits/started minute).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/video-transcript-api
     *
     * @param VideoTranscriptRequest $req
     *
     * @return ApiResponse<Transcript>
     */
    public function transcript(VideoTranscriptRequest $req): ApiResponse
    {
        return $this->postRequest('/video/transcript', $req->toArray(), Transcript::class);
    }

    /**
     * Generate an AI summary from a direct video file URL (5 credits/started minute).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/video-summarize-api
     *
     * @param VideoSummaryRequest $req
     *
     * @return ApiResponse<Summary>
     */
    public function summarize(VideoSummaryRequest $req): ApiResponse
    {
        return $this->postRequest('/video/summarize', $req->toArray(), Summary::class);
    }
}
