<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\BulkRequest;
use Tigusigalpa\SocialKit\Dto\ChannelStatsRequest;
use Tigusigalpa\SocialKit\Dto\Comments;
use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\Download;
use Tigusigalpa\SocialKit\Dto\DownloadRequest;
use Tigusigalpa\SocialKit\Dto\SearchRequest;
use Tigusigalpa\SocialKit\Dto\SearchResult;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\Videos;
use Tigusigalpa\SocialKit\Dto\VideosRequest;
use Tigusigalpa\SocialKit\Dto\YouTubeChannelStats;
use Tigusigalpa\SocialKit\Dto\YouTubeStats;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

/**
 * Service for the SocialKit YouTube endpoints.
 *
 * @see https://docs.socialkit.dev/youtube
 */
final class YouTubeService extends AbstractService
{
    /**
     * Fetch a video transcript.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-transcript-api
     *
     * @param TranscriptRequest $req
     *
     * @return ApiResponse<Transcript>
     */
    public function transcript(TranscriptRequest $req): ApiResponse
    {
        return $this->postRequest('/youtube/transcript', $req->toArray(), Transcript::class);
    }

    /**
     * Generate an AI summary of a video.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-summarize-api
     *
     * @param SummaryRequest $req
     *
     * @return ApiResponse<Summary>
     */
    public function summarize(SummaryRequest $req): ApiResponse
    {
        return $this->postRequest('/youtube/summarize', $req->toArray(), Summary::class);
    }

    /**
     * Fetch video stats (views, likes, comments, etc.).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-stats-api
     *
     * @param StatsRequest $req
     *
     * @return ApiResponse<YouTubeStats>
     */
    public function stats(StatsRequest $req): ApiResponse
    {
        return $this->postRequest('/youtube/stats', $req->toArray(), YouTubeStats::class);
    }

    /**
     * Fetch video comments (limit max 100, sortBy: top|new).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-comments-api
     *
     * @param CommentsRequest $req
     *
     * @return ApiResponse<Comments>
     *
     * @throws BadRequestException If limit > 100 or sortBy is invalid.
     */
    public function comments(CommentsRequest $req): ApiResponse
    {
        if ($req->limit !== null && $req->limit > 100) {
            throw new BadRequestException('YouTube comments limit must not exceed 100.');
        }

        if ($req->sortBy !== null && !in_array($req->sortBy, ['top', 'new'], true)) {
            throw new BadRequestException('YouTube comments sortBy must be "top" or "new".');
        }

        return $this->postRequest('/youtube/comments', $req->toArray(), Comments::class);
    }

    /**
     * Fetch channel stats (subscribers, total videos, etc.).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-channel-stats-api
     *
     * @param ChannelStatsRequest $req
     *
     * @return ApiResponse<YouTubeChannelStats>
     */
    public function channelStats(ChannelStatsRequest $req): ApiResponse
    {
        return $this->postRequest('/youtube/channel-stats', $req->toArray(), YouTubeChannelStats::class);
    }

    /**
     * Search YouTube videos (sortBy: relevance|date|views|rating,
     * uploadDate: hour|today|week|month|year, type: video|shorts).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-search-api
     *
     * @param SearchRequest $req
     *
     * @return ApiResponse<SearchResult>
     *
     * @throws BadRequestException If sortBy, uploadDate, or type is invalid.
     */
    public function search(SearchRequest $req): ApiResponse
    {
        if ($req->sortBy !== null && !in_array($req->sortBy, ['relevance', 'date', 'views', 'rating'], true)) {
            throw new BadRequestException('YouTube search sortBy must be one of: relevance, date, views, rating.');
        }

        if ($req->uploadDate !== null && !in_array($req->uploadDate, ['hour', 'today', 'week', 'month', 'year'], true)) {
            throw new BadRequestException('YouTube search uploadDate must be one of: hour, today, week, month, year.');
        }

        if ($req->type !== null && !in_array($req->type, ['video', 'shorts'], true)) {
            throw new BadRequestException('YouTube search type must be "video" or "shorts".');
        }

        return $this->postRequest('/youtube/search', $req->toArray(), SearchResult::class);
    }

    /**
     * Fetch all videos from a URL (fullDetails, capped at 30).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-videos-api
     *
     * @param VideosRequest $req
     *
     * @return ApiResponse<Videos>
     */
    public function videos(VideosRequest $req): ApiResponse
    {
        $body = $req->toArray();

        // Cap limit at 30
        if (isset($body['limit']) && $body['limit'] > 30) {
            $body['limit'] = 30;
        }

        return $this->postRequest('/youtube/videos', $body, Videos::class);
    }

    /**
     * Download a video (max 10MB, URL expires 1h).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-download-api
     *
     * @param DownloadRequest $req
     *
     * @return ApiResponse<Download>
     */
    public function download(DownloadRequest $req): ApiResponse
    {
        return $this->postRequest('/youtube/download', $req->toArray(), Download::class);
    }

    /**
     * Bulk transcript (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-transcript-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/youtube/transcript/bulk
     *
     * @experimental
     *
     * @param BulkRequest $req
     *
     * @return ApiResponse<array>
     */
    public function bulkTranscript(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/youtube/transcript/bulk', $req->toArray());
    }

    /**
     * Bulk comments (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-comments-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/youtube/comments/bulk
     *
     * @experimental
     *
     * @param BulkRequest $req
     *
     * @return ApiResponse<array>
     */
    public function bulkComments(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/youtube/comments/bulk', $req->toArray());
    }

    /**
     * Bulk stats (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-stats-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/youtube/stats/bulk
     *
     * @experimental
     *
     * @param BulkRequest $req
     *
     * @return ApiResponse<array>
     */
    public function bulkStats(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/youtube/stats/bulk', $req->toArray());
    }

    /**
     * Bulk summarize (experimental).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/youtube-summarize-api and https://api.socialkit.dev/openapi.json (experimental bulk)
     * Advanced API: https://docs.socialkit.dev/advanced/youtube/summarize/bulk
     *
     * @experimental
     *
     * @param BulkRequest $req
     *
     * @return ApiResponse<array>
     */
    public function bulkSummarize(BulkRequest $req): ApiResponse
    {
        return $this->postRawRequest('/youtube/summarize/bulk', $req->toArray());
    }
}
