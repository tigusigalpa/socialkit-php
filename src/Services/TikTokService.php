<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\BulkRequest;
use Tigusigalpa\SocialKit\Dto\ChannelStatsRequest;
use Tigusigalpa\SocialKit\Dto\ChannelVideos;
use Tigusigalpa\SocialKit\Dto\ChannelVideosRequest;
use Tigusigalpa\SocialKit\Dto\Comments;
use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\Download;
use Tigusigalpa\SocialKit\Dto\DownloadRequest;
use Tigusigalpa\SocialKit\Dto\HashtagSearchRequest;
use Tigusigalpa\SocialKit\Dto\SearchRequest;
use Tigusigalpa\SocialKit\Dto\SearchResult;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;
use Tigusigalpa\SocialKit\Dto\TikTokChannelStats;
use Tigusigalpa\SocialKit\Dto\TikTokStats;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

/**
 * Service for the SocialKit TikTok endpoints.
 *
 * @see https://docs.socialkit.dev/tiktok
 */
final class TikTokService extends AbstractService
{
    /**
     * Fetch a video transcript.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-transcript-api
     *
     * @param TranscriptRequest $req
     *
     * @return ApiResponse<Transcript>
     */
    public function transcript(TranscriptRequest $req): ApiResponse
    {
        return $this->postRequest('/tiktok/transcript', $req->toArray(), Transcript::class);
    }

    /**
     * Generate an AI summary of a video.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-summarize-api
     *
     * @param SummaryRequest $req
     *
     * @return ApiResponse<Summary>
     */
    public function summarize(SummaryRequest $req): ApiResponse
    {
        return $this->postRequest('/tiktok/summarize', $req->toArray(), Summary::class);
    }

    /**
     * Fetch video stats.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-stats-api
     *
     * @param StatsRequest $req
     *
     * @return ApiResponse<TikTokStats>
     */
    public function stats(StatsRequest $req): ApiResponse
    {
        return $this->postRequest('/tiktok/stats', $req->toArray(), TikTokStats::class);
    }

    /**
     * Fetch video comments (cursor + hasMore pagination).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-comments-api
     *
     * @param CommentsRequest $req
     *
     * @return ApiResponse<Comments>
     */
    public function comments(CommentsRequest $req): ApiResponse
    {
        return $this->postRequest('/tiktok/comments', $req->toArray(), Comments::class);
    }

    /**
     * Fetch channel stats.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-channel-stats-api
     *
     * @param ChannelStatsRequest $req
     *
     * @return ApiResponse<TikTokChannelStats>
     */
    public function channelStats(ChannelStatsRequest $req): ApiResponse
    {
        return $this->postRequest('/tiktok/channel-stats', $req->toArray(), TikTokChannelStats::class);
    }

    /**
     * Fetch channel videos (default 30, max 100, cursor + hasMore).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-channel-videos-api
     *
     * @param ChannelVideosRequest $req
     *
     * @return ApiResponse<ChannelVideos>
     *
     * @throws BadRequestException If limit > 100.
     */
    public function channelVideos(ChannelVideosRequest $req): ApiResponse
    {
        if ($req->limit !== null && $req->limit > 100) {
            throw new BadRequestException('TikTok channel videos limit must not exceed 100.');
        }

        return $this->postRequest('/tiktok/channel-videos', $req->toArray(), ChannelVideos::class);
    }

    /**
     * Search TikTok videos (sortBy: relevance|likes|date,
     * datePosted: day|week|month|3months|6months, max 100).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-search-api
     *
     * @param SearchRequest $req
     *
     * @return ApiResponse<SearchResult>
     *
     * @throws BadRequestException If sortBy, datePosted invalid or limit > 100.
     */
    public function search(SearchRequest $req): ApiResponse
    {
        if ($req->sortBy !== null && !in_array($req->sortBy, ['relevance', 'likes', 'date'], true)) {
            throw new BadRequestException('TikTok search sortBy must be one of: relevance, likes, date.');
        }

        if ($req->datePosted !== null && !in_array($req->datePosted, ['day', 'week', 'month', '3months', '6months'], true)) {
            throw new BadRequestException('TikTok search datePosted must be one of: day, week, month, 3months, 6months.');
        }

        if ($req->limit !== null && $req->limit > 100) {
            throw new BadRequestException('TikTok search limit must not exceed 100.');
        }

        return $this->postRequest('/tiktok/search', $req->toArray(), SearchResult::class);
    }

    /**
     * Search by hashtag (without #, max 100, cursor + hasMore).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-hashtag-search-api
     *
     * @param HashtagSearchRequest $req
     *
     * @return ApiResponse<SearchResult>
     *
     * @throws BadRequestException If limit > 100.
     */
    public function hashtagSearch(HashtagSearchRequest $req): ApiResponse
    {
        if ($req->limit !== null && $req->limit > 100) {
            throw new BadRequestException('TikTok hashtag search limit must not exceed 100.');
        }

        return $this->postRequest('/tiktok/hashtag-search', $req->toArray(), SearchResult::class);
    }

    /**
     * Download a video (max 30MB, URL expires 1h).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-download-api
     *
     * @param DownloadRequest $req
     *
     * @return ApiResponse<Download>
     */
    public function download(DownloadRequest $req): ApiResponse
    {
        return $this->postRequest('/tiktok/download', $req->toArray(), Download::class);
    }

    /**
     * Bulk transcript (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-transcript-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/tiktok/transcript/bulk
     *
     * @experimental
     */
    public function bulkTranscript(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/tiktok/transcript/bulk', $req->toArray());
    }

    /**
     * Bulk comments (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-comments-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/tiktok/comments/bulk
     *
     * @experimental
     */
    public function bulkComments(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/tiktok/comments/bulk', $req->toArray());
    }

    /**
     * Bulk stats (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-stats-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/tiktok/stats/bulk
     *
     * @experimental
     */
    public function bulkStats(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/tiktok/stats/bulk', $req->toArray());
    }

    /**
     * Bulk channel stats (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-channel-stats-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/tiktok/channel-stats/bulk
     *
     * @experimental
     */
    public function bulkChannelStats(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/tiktok/channel-stats/bulk', $req->toArray());
    }

    /**
     * Bulk summarize (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/tiktok-summarize-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/tiktok/summarize/bulk
     *
     * @experimental
     */
    public function bulkSummarize(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/tiktok/summarize/bulk', $req->toArray());
    }
}
