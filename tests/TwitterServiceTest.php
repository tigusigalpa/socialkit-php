<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\ProfileRequest;
use Tigusigalpa\SocialKit\Dto\ThreadRequest;
use Tigusigalpa\SocialKit\Dto\TweetRequest;
use Tigusigalpa\SocialKit\Dto\TweetsRequest;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\TwitterProfile;
use Tigusigalpa\SocialKit\Dto\TwitterThread;
use Tigusigalpa\SocialKit\Dto\TwitterTweet;
use Tigusigalpa\SocialKit\Dto\TwitterTweets;
use Tigusigalpa\SocialKit\Dto\Transcript;

final class TwitterServiceTest extends TestCase
{
    public function testProfile(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://twitter.com/elonmusk',
                    'id' => '44196397',
                    'name' => 'Elon Musk',
                    'username' => 'elonmusk',
                    'bio' => 'CEO of Tesla, SpaceX',
                    'followers' => 200000000,
                    'following' => 100,
                    'tweets' => 30000,
                    'verified' => true,
                    'location' => 'Mars',
                    'joined_at' => '2009-06-02',
                ],
            ]),
        ]);

        $response = $client->twitter()->profile(new ProfileRequest(
            url: 'https://twitter.com/elonmusk',
        ));

        self::assertInstanceOf(TwitterProfile::class, $response->data);
        self::assertSame('Elon Musk', $response->data->name);
        self::assertSame('elonmusk', $response->data->username);
        self::assertSame(200000000, $response->data->followers);
        self::assertTrue($response->data->verified);
    }

    public function testTweets(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'tweets' => [
                        ['id' => '1', 'text' => 'Tweet 1', 'likes' => 100, 'retweets' => 50],
                        ['id' => '2', 'text' => 'Tweet 2', 'likes' => 200, 'retweets' => 100],
                    ],
                    'next_cursor' => 'abc123',
                ],
            ]),
        ]);

        $response = $client->twitter()->tweets(new TweetsRequest(
            url: 'https://twitter.com/elonmusk',
            limit: 10,
        ));

        self::assertInstanceOf(TwitterTweets::class, $response->data);
        self::assertCount(2, $response->data->tweets);
        self::assertSame('Tweet 1', $response->data->tweets[0]->text);
        self::assertSame(100, $response->data->tweets[0]->likes);
        self::assertSame('abc123', $response->data->nextCursor);
    }

    public function testTweetsWithNullNextCursor(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'tweets' => [],
                    'next_cursor' => null,
                ],
            ]),
        ]);

        $response = $client->twitter()->tweets(new TweetsRequest(
            url: 'https://twitter.com/test',
        ));

        self::assertInstanceOf(TwitterTweets::class, $response->data);
        self::assertNull($response->data->nextCursor);
    }

    public function testTweet(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'id' => '123',
                    'text' => 'Single tweet',
                    'likes' => 500,
                    'retweets' => 100,
                    'replies' => 50,
                    'views' => 10000,
                    'created_at' => '2026-01-01',
                    'author' => 'user',
                    'hashtags' => ['PHP'],
                    'urls' => ['https://example.com'],
                ],
            ]),
        ]);

        $response = $client->twitter()->tweet(new TweetRequest(
            url: 'https://twitter.com/user/status/123',
        ));

        self::assertInstanceOf(TwitterTweet::class, $response->data);
        self::assertSame('Single tweet', $response->data->text);
        self::assertSame(500, $response->data->likes);
        self::assertSame(['PHP'], $response->data->hashtags);
    }

    public function testThread(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'conversation_id' => '123',
                    'tweet_count' => 3,
                    'is_thread' => true,
                    'combined_text' => 'Thread text combined',
                    'author' => ['name' => 'User', 'username' => 'user'],
                    'tweets' => [
                        ['id' => '1', 'text' => 'Tweet 1'],
                        ['id' => '2', 'text' => 'Tweet 2'],
                        ['id' => '3', 'text' => 'Tweet 3'],
                    ],
                ],
            ]),
        ]);

        $response = $client->twitter()->thread(new ThreadRequest(
            url: 'https://twitter.com/user/status/123',
        ));

        self::assertInstanceOf(TwitterThread::class, $response->data);
        self::assertSame('123', $response->data->conversationId);
        self::assertSame(3, $response->data->tweetCount);
        self::assertTrue($response->data->isThread);
        self::assertSame('Thread text combined', $response->data->combinedText);
        self::assertCount(3, $response->data->tweets);
        self::assertSame('User', $response->data->author['name']);
    }

    public function testTranscript(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://twitter.com/user/status/123',
                    'transcript' => 'Twitter video transcript',
                ],
            ]),
        ]);

        $response = $client->twitter()->transcript(new TranscriptRequest(
            url: 'https://twitter.com/user/status/123',
        ));

        self::assertInstanceOf(Transcript::class, $response->data);
        self::assertSame('Twitter video transcript', $response->data->transcript);
    }
}
