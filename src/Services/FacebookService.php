<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\ChannelStatsRequest;
use Tigusigalpa\SocialKit\Dto\Comments;
use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\FacebookChannelStats;
use Tigusigalpa\SocialKit\Dto\FacebookStats;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

/**
 * Service for the SocialKit Facebook endpoints.
 *
 * Note: Facebook has NO sync download and NO bulk endpoints.
 *
 * @see https://docs.socialkit.dev/facebook
 */
final class FacebookService extends AbstractService
{
    /**
     * Fetch a video transcript.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/facebook-transcript-api
     *
     * @param TranscriptRequest $req
     *
     * @return ApiResponse<Transcript>
     */
    public function transcript(TranscriptRequest $req): ApiResponse
    {
        return $this->postRequest('/facebook/transcript', $req->toArray(), Transcript::class);
    }

    /**
     * Generate an AI summary of a video.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/facebook-summarize-api
     *
     * @param SummaryRequest $req
     *
     * @return ApiResponse<Summary>
     */
    public function summarize(SummaryRequest $req): ApiResponse
    {
        return $this->postRequest('/facebook/summarize', $req->toArray(), Summary::class);
    }

    /**
     * Fetch post stats (with reactions[]).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/facebook-stats-api
     *
     * @param StatsRequest $req
     *
     * @return ApiResponse<FacebookStats>
     */
    public function stats(StatsRequest $req): ApiResponse
    {
        return $this->postRequest('/facebook/stats', $req->toArray(), FacebookStats::class);
    }

    /**
     * Fetch post comments (cursor + hasMore, commentCount nullable, max 100).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/facebook-comments-api
     *
     * @param CommentsRequest $req
     *
     * @return ApiResponse<Comments>
     *
     * @throws BadRequestException If limit > 100.
     */
    public function comments(CommentsRequest $req): ApiResponse
    {
        if ($req->limit !== null && $req->limit > 100) {
            throw new BadRequestException('Facebook comments limit must not exceed 100.');
        }

        return $this->postRequest('/facebook/comments', $req->toArray(), Comments::class);
    }

    /**
     * Fetch channel/page stats.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/facebook-channel-stats-api
     *
     * @param ChannelStatsRequest $req
     *
     * @return ApiResponse<FacebookChannelStats>
     */
    public function channelStats(ChannelStatsRequest $req): ApiResponse
    {
        return $this->postRequest('/facebook/channel-stats', $req->toArray(), FacebookChannelStats::class);
    }
}
