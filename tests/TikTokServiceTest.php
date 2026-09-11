<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\ChannelStatsRequest;
use Tigusigalpa\SocialKit\Dto\ChannelVideosRequest;
use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\DownloadRequest;
use Tigusigalpa\SocialKit\Dto\HashtagSearchRequest;
use Tigusigalpa\SocialKit\Dto\SearchRequest;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\ChannelVideos;
use Tigusigalpa\SocialKit\Dto\Comments;
use Tigusigalpa\SocialKit\Dto\Download;
use Tigusigalpa\SocialKit\Dto\SearchResult;
use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\TikTokChannelStats;
use Tigusigalpa\SocialKit\Dto\TikTokStats;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

final class TikTokServiceTest extends TestCase
{
    public function testTranscript(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://tiktok.com/@user/video/123',
                    'transcript' => 'TikTok transcript text',
                    'credits_used' => 1.0,
                ],
            ]),
        ]);

        $response = $client->tiktok()->transcript(new TranscriptRequest(
            url: 'https://tiktok.com/@user/video/123',
        ));

        self::assertInstanceOf(Transcript::class, $response->data);
        self::assertSame('TikTok transcript text', $response->data->transcript);
    }

    public function testSummarize(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => ['url' => 'https://tiktok.com/@user/video/123', 'summary' => 'TikTok summary'],
            ]),
        ]);

        $response = $client->tiktok()->summarize(new SummaryRequest(
            url: 'https://tiktok.com/@user/video/123',
        ));

        self::assertInstanceOf(Summary::class, $response->data);
        self::assertSame('TikTok summary', $response->data->summary);
    }

    public function testStats(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://tiktok.com/@user/video/123',
                    'video_id' => '123',
                    'title' => 'TikTok Video',
                    'channel_name' => 'user',
                    'views' => 50000,
                    'likes' => 10000,
                    'comments' => 500,
                    'shares' => 200,
                    'collects' => 100,
                ],
            ]),
        ]);

        $response = $client->tiktok()->stats(new StatsRequest(
            url: 'https://tiktok.com/@user/video/123',
        ));

        self::assertInstanceOf(TikTokStats::class, $response->data);
        self::assertSame('TikTok Video', $response->data->title);
        self::assertSame(50000, $response->data->views);
        self::assertSame(10000, $response->data->likes);
        self::assertSame(100, $response->data->collects);
    }

    public function testComments(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'comments' => [
                        ['id' => '1', 'text' => 'Nice', 'likes' => 10, 'author' => 'user1'],
                    ],
                    'has_more' => true,
                    'cursor' => 'next123',
                ],
            ]),
        ]);

        $response = $client->tiktok()->comments(new CommentsRequest(
            url: 'https://tiktok.com/@user/video/123',
            cursor: 'prev123',
        ));

        self::assertInstanceOf(Comments::class, $response->data);
        self::assertCount(1, $response->data->comments);
        self::assertTrue($response->data->hasMore);
        self::assertSame('next123', $response->data->cursor);
    }

    public function testChannelStats(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'profile_url' => 'https://tiktok.com/@user',
                    'username' => 'user',
                    'nickname' => 'User Name',
                    'followers' => 1000000,
                    'following' => 100,
                    'total_likes' => 50000000,
                    'total_videos' => 500,
                    'verified' => true,
                ],
            ]),
        ]);

        $response = $client->tiktok()->channelStats(new ChannelStatsRequest(
            url: 'https://tiktok.com/@user',
        ));

        self::assertInstanceOf(TikTokChannelStats::class, $response->data);
        self::assertSame(1000000, $response->data->followers);
        self::assertTrue($response->data->verified);
    }

    public function testChannelVideos(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'profile_url' => 'https://tiktok.com/@user',
                    'channel_name' => 'user',
                    'results' => [
                        ['video_id' => '1', 'description' => 'Video 1', 'views' => 1000],
                    ],
                    'has_more' => true,
                    'cursor' => 'next',
                ],
            ]),
        ]);

        $response = $client->tiktok()->channelVideos(new ChannelVideosRequest(
            url: 'https://tiktok.com/@user',
            limit: 30,
        ));

        self::assertInstanceOf(ChannelVideos::class, $response->data);
        self::assertCount(1, $response->data->results);
        self::assertTrue($response->data->hasMore);
    }

    public function testChannelVideosLimitExceeds100(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->makeClient([]);
        $client->tiktok()->channelVideos(new ChannelVideosRequest(
            url: 'https://tiktok.com/@user',
            limit: 101,
        ));
    }

    public function testSearch(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'query' => 'dance',
                    'results' => [['id' => '1', 'desc' => 'Dance video']],
                    'has_more' => false,
                ],
            ]),
        ]);

        $response = $client->tiktok()->search(new SearchRequest(
            query: 'dance',
            sortBy: 'likes',
            datePosted: 'week',
        ));

        self::assertInstanceOf(SearchResult::class, $response->data);
        self::assertFalse($response->data->hasMore);
    }

    public function testSearchInvalidSortBy(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->makeClient([]);
        $client->tiktok()->search(new SearchRequest(query: 'test', sortBy: 'invalid'));
    }

    public function testHashtagSearch(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'query' => 'fyp',
                    'results' => [['id' => '1', 'desc' => 'FYP video']],
                    'has_more' => true,
                    'cursor' => 'abc',
                ],
            ]),
        ]);

        $response = $client->tiktok()->hashtagSearch(new HashtagSearchRequest(
            hashtag: 'fyp',
            limit: 50,
        ));

        self::assertInstanceOf(SearchResult::class, $response->data);
        self::assertTrue($response->data->hasMore);

        $body = json_decode((string) $this->lastRequest()->getBody(), true);
        self::assertSame('fyp', $body['hashtag']);
    }

    public function testDownload(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://tiktok.com/@user/video/123',
                    'download_url' => 'https://cdn.socialkit.dev/dl/tiktok.mp4',
                    'format' => 'mp4',
                    'expires_in' => 3600,
                ],
            ]),
        ]);

        $response = $client->tiktok()->download(new DownloadRequest(
            url: 'https://tiktok.com/@user/video/123',
        ));

        self::assertInstanceOf(Download::class, $response->data);
        self::assertSame('https://cdn.socialkit.dev/dl/tiktok.mp4', $response->data->downloadUrl);
    }
}
