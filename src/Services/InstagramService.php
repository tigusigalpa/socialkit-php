<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\BulkRequest;
use Tigusigalpa\SocialKit\Dto\ChannelPosts;
use Tigusigalpa\SocialKit\Dto\ChannelPostsRequest;
use Tigusigalpa\SocialKit\Dto\ChannelReels;
use Tigusigalpa\SocialKit\Dto\ChannelReelsRequest;
use Tigusigalpa\SocialKit\Dto\ChannelStatsRequest;
use Tigusigalpa\SocialKit\Dto\Comments;
use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\Download;
use Tigusigalpa\SocialKit\Dto\DownloadRequest;
use Tigusigalpa\SocialKit\Dto\InstagramChannelStats;
use Tigusigalpa\SocialKit\Dto\InstagramStats;
use Tigusigalpa\SocialKit\Dto\ReelsSearch;
use Tigusigalpa\SocialKit\Dto\ReelsSearchRequest;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

/**
 * Service for the SocialKit Instagram endpoints.
 *
 * @see https://docs.socialkit.dev/instagram
 */
final class InstagramService extends AbstractService
{
    /**
     * Fetch a video transcript.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-transcript-api
     *
     * @param TranscriptRequest $req
     *
     * @return ApiResponse<Transcript>
     */
    public function transcript(TranscriptRequest $req): ApiResponse
    {
        return $this->postRequest('/instagram/transcript', $req->toArray(), Transcript::class);
    }

    /**
     * Generate an AI summary of a video.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-summarize-api
     *
     * @param SummaryRequest $req
     *
     * @return ApiResponse<Summary>
     */
    public function summarize(SummaryRequest $req): ApiResponse
    {
        return $this->postRequest('/instagram/summarize', $req->toArray(), Summary::class);
    }

    /**
     * Fetch post stats.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-stats-api
     *
     * @param StatsRequest $req
     *
     * @return ApiResponse<InstagramStats>
     */
    public function stats(StatsRequest $req): ApiResponse
    {
        return $this->postRequest('/instagram/stats', $req->toArray(), InstagramStats::class);
    }

    /**
     * Fetch post comments (cursor + hasMore).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-comments-api
     *
     * @param CommentsRequest $req
     *
     * @return ApiResponse<Comments>
     */
    public function comments(CommentsRequest $req): ApiResponse
    {
        return $this->postRequest('/instagram/comments', $req->toArray(), Comments::class);
    }

    /**
     * Fetch channel stats.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-channel-stats-api
     *
     * @param ChannelStatsRequest $req
     *
     * @return ApiResponse<InstagramChannelStats>
     */
    public function channelStats(ChannelStatsRequest $req): ApiResponse
    {
        return $this->postRequest('/instagram/channel-stats', $req->toArray(), InstagramChannelStats::class);
    }

    /**
     * Fetch channel posts (cursor + hasMore, max 100, 1 credit/20 results).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-channel-posts-api
     *
     * @param ChannelPostsRequest $req
     *
     * @return ApiResponse<ChannelPosts>
     *
     * @throws BadRequestException If limit > 100.
     */
    public function channelPosts(ChannelPostsRequest $req): ApiResponse
    {
        if ($req->limit !== null && $req->limit > 100) {
            throw new BadRequestException('Instagram channel posts limit must not exceed 100.');
        }

        return $this->postRequest('/instagram/channel-posts', $req->toArray(), ChannelPosts::class);
    }

    /**
     * Fetch channel reels (cursor + hasMore, can take 60s).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-channel-reels-api
     *
     * @param ChannelReelsRequest $req
     *
     * @return ApiResponse<ChannelReels>
     */
    public function channelReels(ChannelReelsRequest $req): ApiResponse
    {
        return $this->postRequest('/instagram/channel-reels', $req->toArray(), ChannelReels::class);
    }

    /**
     * Search reels (page must be 1, hasMore always false).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-reels-search-api
     *
     * @param ReelsSearchRequest $req
     *
     * @return ApiResponse<ReelsSearch>
     */
    public function reelsSearch(ReelsSearchRequest $req): ApiResponse
    {
        return $this->postRequest('/instagram/reels-search', $req->toArray(), ReelsSearch::class);
    }

    /**
     * Download a video (URL expires 1h).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-download-api
     *
     * @param DownloadRequest $req
     *
     * @return ApiResponse<Download>
     */
    public function download(DownloadRequest $req): ApiResponse
    {
        return $this->postRequest('/instagram/download', $req->toArray(), Download::class);
    }

    /**
     * Bulk transcript (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-transcript-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/instagram/transcript/bulk
     *
     * @experimental
     */
    public function bulkTranscript(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/instagram/transcript/bulk', $req->toArray());
    }

    /**
     * Bulk stats (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-stats-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/instagram/stats/bulk
     *
     * @experimental
     */
    public function bulkStats(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/instagram/stats/bulk', $req->toArray());
    }

    /**
     * Bulk channel stats (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-channel-stats-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/instagram/channel-stats/bulk
     *
     * @experimental
     */
    public function bulkChannelStats(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/instagram/channel-stats/bulk', $req->toArray());
    }

    /**
     * Bulk summarize (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/instagram-summarize-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/instagram/summarize/bulk
     *
     * @experimental
     */
    public function bulkSummarize(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/instagram/summarize/bulk', $req->toArray());
    }
}
