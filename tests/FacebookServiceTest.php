<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\ChannelStatsRequest;
use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\Comments;
use Tigusigalpa\SocialKit\Dto\FacebookChannelStats;
use Tigusigalpa\SocialKit\Dto\FacebookStats;
use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

final class FacebookServiceTest extends TestCase
{
    public function testTranscript(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => ['url' => 'https://facebook.com/watch?v=123', 'transcript' => 'FB transcript'],
            ]),
        ]);

        $response = $client->facebook()->transcript(new TranscriptRequest(
            url: 'https://facebook.com/watch?v=123',
        ));

        self::assertInstanceOf(Transcript::class, $response->data);
    }

    public function testSummarize(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => ['url' => 'https://facebook.com/watch?v=123', 'summary' => 'FB summary'],
            ]),
        ]);

        $response = $client->facebook()->summarize(new SummaryRequest(
            url: 'https://facebook.com/watch?v=123',
        ));

        self::assertInstanceOf(Summary::class, $response->data);
    }

    public function testStatsWithReactions(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'post_url' => 'https://facebook.com/post/123',
                    'id' => '123',
                    'description' => 'FB post description',
                    'views' => 10000,
                    'likes' => 500,
                    'comments' => 50,
                    'shares' => 20,
                    'reactions' => [
                        ['name' => 'like', 'count' => 300, 'formatted' => '300'],
                        ['name' => 'love', 'count' => 150, 'formatted' => '150'],
                        ['name' => 'wow', 'count' => 50, 'formatted' => '50'],
                    ],
                    'author' => 'Test Page',
                    'is_video' => true,
                ],
            ]),
        ]);

        $response = $client->facebook()->stats(new StatsRequest(
            url: 'https://facebook.com/post/123',
        ));

        self::assertInstanceOf(FacebookStats::class, $response->data);
        self::assertSame(500, $response->data->likes);
        self::assertCount(3, $response->data->reactions);
        self::assertSame('like', $response->data->reactions[0]->name);
        self::assertSame(300, $response->data->reactions[0]->count);
        self::assertSame('300', $response->data->reactions[0]->formatted);
        self::assertTrue($response->data->isVideo);
    }

    public function testComments(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'comments' => [['id' => '1', 'text' => 'FB comment']],
                    'comment_count' => 1,
                    'has_more' => false,
                ],
            ]),
        ]);

        $response = $client->facebook()->comments(new CommentsRequest(
            url: 'https://facebook.com/post/123',
        ));

        self::assertInstanceOf(Comments::class, $response->data);
        self::assertSame(1, $response->data->commentCount);
        self::assertFalse($response->data->hasMore);
    }

    public function testCommentsLimitExceeds100(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->makeClient([]);
        $client->facebook()->comments(new CommentsRequest(
            url: 'https://facebook.com/post/123',
            limit: 101,
        ));
    }

    public function testChannelStats(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'profile_url' => 'https://facebook.com/testpage',
                    'id' => '123',
                    'full_name' => 'Test Page',
                    'bio' => 'Test bio',
                    'followers' => 10000,
                    'verified' => true,
                    'category' => 'Brand',
                ],
            ]),
        ]);

        $response = $client->facebook()->channelStats(new ChannelStatsRequest(
            url: 'https://facebook.com/testpage',
        ));

        self::assertInstanceOf(FacebookChannelStats::class, $response->data);
        self::assertSame(10000, $response->data->followers);
        self::assertTrue($response->data->verified);
        self::assertSame('Brand', $response->data->category);
    }
}
