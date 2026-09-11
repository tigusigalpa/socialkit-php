<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\ChannelPostsRequest;
use Tigusigalpa\SocialKit\Dto\ChannelReelsRequest;
use Tigusigalpa\SocialKit\Dto\ChannelStatsRequest;
use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\DownloadRequest;
use Tigusigalpa\SocialKit\Dto\ReelsSearchRequest;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\ChannelPosts;
use Tigusigalpa\SocialKit\Dto\ChannelReels;
use Tigusigalpa\SocialKit\Dto\Comments;
use Tigusigalpa\SocialKit\Dto\Download;
use Tigusigalpa\SocialKit\Dto\InstagramChannelStats;
use Tigusigalpa\SocialKit\Dto\InstagramStats;
use Tigusigalpa\SocialKit\Dto\ReelsSearch;
use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

final class InstagramServiceTest extends TestCase
{
    public function testTranscript(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => ['url' => 'https://instagram.com/reel/abc', 'transcript' => 'IG transcript'],
            ]),
        ]);

        $response = $client->instagram()->transcript(new TranscriptRequest(
            url: 'https://instagram.com/reel/abc',
        ));

        self::assertInstanceOf(Transcript::class, $response->data);
    }

    public function testSummarize(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => ['url' => 'https://instagram.com/reel/abc', 'summary' => 'IG summary'],
            ]),
        ]);

        $response = $client->instagram()->summarize(new SummaryRequest(
            url: 'https://instagram.com/reel/abc',
        ));

        self::assertInstanceOf(Summary::class, $response->data);
    }

    public function testStats(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'post_url' => 'https://instagram.com/p/abc',
                    'id' => 'abc',
                    'shortcode' => 'abc',
                    'likes' => 1000,
                    'comments' => 50,
                    'views' => 5000,
                    'is_video' => true,
                ],
            ]),
        ]);

        $response = $client->instagram()->stats(new StatsRequest(
            url: 'https://instagram.com/p/abc',
        ));

        self::assertInstanceOf(InstagramStats::class, $response->data);
        self::assertSame(1000, $response->data->likes);
        self::assertTrue($response->data->isVideo);
    }

    public function testComments(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'comments' => [['id' => '1', 'text' => 'Nice!']],
                    'has_more' => true,
                    'cursor' => 'next',
                ],
            ]),
        ]);

        $response = $client->instagram()->comments(new CommentsRequest(
            url: 'https://instagram.com/p/abc',
        ));

        self::assertInstanceOf(Comments::class, $response->data);
        self::assertTrue($response->data->hasMore);
    }

    public function testChannelStats(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'profile_url' => 'https://instagram.com/test',
                    'username' => 'test',
                    'full_name' => 'Test User',
                    'followers' => 50000,
                    'following' => 100,
                    'total_posts' => 200,
                    'verified' => false,
                ],
            ]),
        ]);

        $response = $client->instagram()->channelStats(new ChannelStatsRequest(
            url: 'https://instagram.com/test',
        ));

        self::assertInstanceOf(InstagramChannelStats::class, $response->data);
        self::assertSame(50000, $response->data->followers);
        self::assertFalse($response->data->verified);
    }

    public function testChannelPosts(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'profile_url' => 'https://instagram.com/test',
                    'username' => 'test',
                    'items' => [
                        ['id' => '1', 'shortcode' => 'abc', 'type' => 'image', 'likes' => 100],
                    ],
                    'count' => 1,
                    'has_more' => true,
                    'cursor' => 'next',
                ],
            ]),
        ]);

        $response = $client->instagram()->channelPosts(new ChannelPostsRequest(
            url: 'https://instagram.com/test',
            limit: 20,
        ));

        self::assertInstanceOf(ChannelPosts::class, $response->data);
        self::assertCount(1, $response->data->items);
        self::assertTrue($response->data->hasMore);
    }

    public function testChannelPostsLimitExceeds100(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->makeClient([]);
        $client->instagram()->channelPosts(new ChannelPostsRequest(
            url: 'https://instagram.com/test',
            limit: 101,
        ));
    }

    public function testChannelReels(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'profile_url' => 'https://instagram.com/test',
                    'username' => 'test',
                    'items' => [
                        ['id' => '1', 'shortcode' => 'abc', 'likes' => 200, 'views' => 5000],
                    ],
                    'count' => 1,
                    'has_more' => false,
                ],
            ]),
        ]);

        $response = $client->instagram()->channelReels(new ChannelReelsRequest(
            url: 'https://instagram.com/test',
        ));

        self::assertInstanceOf(ChannelReels::class, $response->data);
        self::assertCount(1, $response->data->items);
    }

    public function testReelsSearch(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'query' => 'travel',
                    'items' => [
                        ['id' => '1', 'url' => 'https://instagram.com/reel/1', 'likes' => 1000],
                    ],
                    'count' => 1,
                    'has_more' => false,
                ],
            ]),
        ]);

        $response = $client->instagram()->reelsSearch(new ReelsSearchRequest(
            query: 'travel',
        ));

        self::assertInstanceOf(ReelsSearch::class, $response->data);
        self::assertCount(1, $response->data->items);
        self::assertFalse($response->data->hasMore);
    }

    public function testReelsSearchPageGreaterThan1Throws(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->makeClient([]);
        $client->instagram()->reelsSearch(new ReelsSearchRequest(
            query: 'travel',
            page: 2,
        ));
    }

    public function testDownload(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://instagram.com/reel/abc',
                    'download_url' => 'https://cdn.socialkit.dev/dl/ig.mp4',
                    'expires_in' => 3600,
                ],
            ]),
        ]);

        $response = $client->instagram()->download(new DownloadRequest(
            url: 'https://instagram.com/reel/abc',
        ));

        self::assertInstanceOf(Download::class, $response->data);
    }
}
