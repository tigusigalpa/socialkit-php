<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\BulkRequest;
use Tigusigalpa\SocialKit\Dto\ChannelStatsRequest;
use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\DownloadRequest;
use Tigusigalpa\SocialKit\Dto\SearchRequest;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\VideosRequest;
use Tigusigalpa\SocialKit\Dto\YouTubeChannelStats;
use Tigusigalpa\SocialKit\Dto\YouTubeStats;
use Tigusigalpa\SocialKit\Dto\Comments;
use Tigusigalpa\SocialKit\Dto\Download;
use Tigusigalpa\SocialKit\Dto\SearchResult;
use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Dto\Videos;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

final class YouTubeServiceTest extends TestCase
{
    public function testTranscript(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://youtube.com/watch?v=dQw4w9WgXcQ',
                    'video_id' => 'dQw4w9WgXcQ',
                    'transcript' => 'Never gonna give you up...',
                    'transcript_segments' => [
                        ['text' => 'Never gonna give you up', 'start' => 0.0, 'duration' => 3.5],
                    ],
                    'word_count' => 500,
                    'segments' => 1,
                    'language' => 'en',
                    'duration_seconds' => 213.0,
                    'credits_used' => 1.0,
                ],
            ]),
        ]);

        $response = $client->youtube()->transcript(new TranscriptRequest(
            url: 'https://youtube.com/watch?v=dQw4w9WgXcQ',
        ));

        self::assertInstanceOf(Transcript::class, $response->data);
        self::assertSame('dQw4w9WgXcQ', $response->data->videoId);
        self::assertSame('Never gonna give you up...', $response->data->transcript);
        self::assertCount(1, $response->data->transcriptSegments);
        self::assertSame('en', $response->data->language);
        self::assertSame(1.0, $response->data->creditsUsed);

        $body = json_decode((string) $this->lastRequest()->getBody(), true);
        self::assertSame('https://youtube.com/watch?v=dQw4w9WgXcQ', $body['url']);
        self::assertFalse($body['cache']);
    }

    public function testSummarize(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://youtube.com/watch?v=test',
                    'summary' => 'A great video about PHP.',
                    'main_topics' => ['PHP', 'SDK'],
                    'key_points' => ['Point 1', 'Point 2'],
                    'tone' => 'informative',
                    'target_audience' => 'developers',
                    'quotes' => ['Quote 1'],
                    'duration_seconds' => 300.0,
                    'credits_used' => 2.0,
                ],
            ]),
        ]);

        $response = $client->youtube()->summarize(new SummaryRequest(
            url: 'https://youtube.com/watch?v=test',
        ));

        self::assertInstanceOf(Summary::class, $response->data);
        self::assertSame('A great video about PHP.', $response->data->summary);
        self::assertSame(['PHP', 'SDK'], $response->data->mainTopics);
        self::assertSame(['Point 1', 'Point 2'], $response->data->keyPoints);
        self::assertSame('informative', $response->data->tone);
    }

    public function testSummarizeWithCustomResponse(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://youtube.com/watch?v=test',
                    'summary' => 'Test',
                    'custom_field' => 'custom_value',
                ],
            ]),
        ]);

        $response = $client->youtube()->summarize(new SummaryRequest(
            url: 'https://youtube.com/watch?v=test',
            customResponse: '{"sentiment": "positive"}',
        ));

        self::assertInstanceOf(Summary::class, $response->data);
        self::assertArrayHasKey('custom_field', $response->data->extra);

        $body = json_decode((string) $this->lastRequest()->getBody(), true);
        self::assertArrayHasKey('custom_response', $body);
        self::assertSame('positive', $body['custom_response']['sentiment']);
    }

    public function testStats(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://youtube.com/watch?v=test',
                    'video_id' => 'test',
                    'title' => 'Test Video',
                    'channel_name' => 'Test Channel',
                    'views' => 1000000,
                    'likes' => 50000,
                    'comments' => 1000,
                    'duration' => '10:30',
                    'published_at' => '2026-01-01',
                    'content_type' => 'video',
                    'is_short_form' => false,
                ],
            ]),
        ]);

        $response = $client->youtube()->stats(new StatsRequest(
            url: 'https://youtube.com/watch?v=test',
        ));

        self::assertInstanceOf(YouTubeStats::class, $response->data);
        self::assertSame('Test Video', $response->data->title);
        self::assertSame(1000000, $response->data->views);
        self::assertSame(50000, $response->data->likes);
        self::assertFalse($response->data->isShortForm);
    }

    public function testComments(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'video_id' => 'test',
                    'comments' => [
                        ['id' => '1', 'text' => 'Great video', 'like_count' => 10, 'author' => 'User1'],
                        ['id' => '2', 'text' => 'Nice', 'like_count' => 5, 'author' => 'User2'],
                    ],
                    'comment_count' => 2,
                    'has_more' => false,
                ],
            ]),
        ]);

        $response = $client->youtube()->comments(new CommentsRequest(
            url: 'https://youtube.com/watch?v=test',
            limit: 50,
            sortBy: 'top',
        ));

        self::assertInstanceOf(Comments::class, $response->data);
        self::assertCount(2, $response->data->comments);
        self::assertSame('Great video', $response->data->comments[0]->text);
        self::assertSame(10, $response->data->comments[0]->likeCount);
    }

    public function testCommentsLimitExceeds100(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->makeClient([]);
        $client->youtube()->comments(new CommentsRequest(
            url: 'https://youtube.com/watch?v=test',
            limit: 101,
        ));
    }

    public function testCommentsInvalidSortBy(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->makeClient([]);
        $client->youtube()->comments(new CommentsRequest(
            url: 'https://youtube.com/watch?v=test',
            sortBy: 'invalid',
        ));
    }

    public function testChannelStats(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'profile_url' => 'https://youtube.com/@test',
                    'username' => 'test',
                    'nickname' => 'Test Channel',
                    'subscribers' => 100000,
                    'total_videos' => 500,
                    'verified' => true,
                ],
            ]),
        ]);

        $response = $client->youtube()->channelStats(new ChannelStatsRequest(
            url: 'https://youtube.com/@test',
        ));

        self::assertInstanceOf(YouTubeChannelStats::class, $response->data);
        self::assertSame('Test Channel', $response->data->nickname);
        self::assertSame(100000, $response->data->subscribers);
        self::assertTrue($response->data->verified);
    }

    public function testSearch(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'query' => 'PHP tutorial',
                    'results' => [
                        ['video_id' => '1', 'title' => 'PHP Basics', 'views' => 1000],
                    ],
                    'has_more' => true,
                    'cursor' => 'abc123',
                ],
            ]),
        ]);

        $response = $client->youtube()->search(new SearchRequest(
            query: 'PHP tutorial',
            limit: 10,
            sortBy: 'relevance',
        ));

        self::assertInstanceOf(SearchResult::class, $response->data);
        self::assertSame('PHP tutorial', $response->data->query);
        self::assertTrue($response->data->hasMore);
        self::assertSame('abc123', $response->data->cursor);
    }

    public function testSearchInvalidSortBy(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->makeClient([]);
        $client->youtube()->search(new SearchRequest(
            query: 'test',
            sortBy: 'invalid',
        ));
    }

    public function testVideos(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://youtube.com/@test',
                    'type' => 'channel',
                    'results' => [
                        ['video_id' => '1', 'title' => 'Video 1'],
                    ],
                ],
            ]),
        ]);

        $response = $client->youtube()->videos(new VideosRequest(
            url: 'https://youtube.com/@test',
            limit: 50,
            fullDetails: true,
        ));

        self::assertInstanceOf(Videos::class, $response->data);
        self::assertCount(1, $response->data->results);

        // Limit should be capped at 30
        $body = json_decode((string) $this->lastRequest()->getBody(), true);
        self::assertSame(30, $body['limit']);
        self::assertTrue($body['full_details']);
    }

    public function testDownload(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://youtube.com/watch?v=test',
                    'title' => 'Test Video',
                    'download_url' => 'https://cdn.socialkit.dev/dl/test.mp4',
                    'format' => 'mp4',
                    'quality' => '720p',
                    'expires_in' => 3600,
                    'credits_used' => 5.0,
                ],
            ]),
        ]);

        $response = $client->youtube()->download(new DownloadRequest(
            url: 'https://youtube.com/watch?v=test',
        ));

        self::assertInstanceOf(Download::class, $response->data);
        self::assertSame('https://cdn.socialkit.dev/dl/test.mp4', $response->data->downloadUrl);
        self::assertSame(3600, $response->data->expiresIn);
    }

    public function testBulkTranscript(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => [['url' => 'https://test.com', 'transcript' => 'test']]]),
        ]);

        $response = $client->youtube()->bulkTranscript(new BulkRequest(
            requests: [['url' => 'https://youtube.com/watch?v=1'], ['url' => 'https://youtube.com/watch?v=2']],
        ));

        self::assertTrue($response->success);

        $body = json_decode((string) $this->lastRequest()->getBody(), true);
        self::assertCount(2, $body['requests']);
    }
}
