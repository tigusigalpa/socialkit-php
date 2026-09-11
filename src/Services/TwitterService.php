<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\ProfileRequest;
use Tigusigalpa\SocialKit\Dto\ThreadRequest;
use Tigusigalpa\SocialKit\Dto\TweetRequest;
use Tigusigalpa\SocialKit\Dto\TweetsRequest;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\TwitterProfile;
use Tigusigalpa\SocialKit\Dto\TwitterThread;
use Tigusigalpa\SocialKit\Dto\TwitterTweets;

/**
 * Service for the SocialKit Twitter/X endpoints.
 *
 * @see https://docs.socialkit.dev/twitter
 */
final class TwitterService extends AbstractService
{
    /**
     * Fetch a Twitter profile.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/twitter-profile-api
     *
     * @param ProfileRequest $req
     *
     * @return ApiResponse<TwitterProfile>
     */
    public function profile(ProfileRequest $req): ApiResponse
    {
        return $this->postRequest('/twitter/profile', $req->toArray(), TwitterProfile::class);
    }

    /**
     * Fetch a user's tweets (nextCursor nullable, use NextPage helper).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/twitter-tweets-api
     *
     * @param TweetsRequest $req
     *
     * @return ApiResponse<TwitterTweets>
     */
    public function tweets(TweetsRequest $req): ApiResponse
    {
        return $this->postRequest('/twitter/tweets', $req->toArray(), TwitterTweets::class);
    }

    /**
     * Fetch a single tweet.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/twitter-tweet-api
     *
     * @param TweetRequest $req
     *
     * @return ApiResponse<\Tigusigalpa\SocialKit\Dto\TwitterTweet>
     */
    public function tweet(TweetRequest $req): ApiResponse
    {
        return $this->postRequest('/twitter/tweet', $req->toArray(), \Tigusigalpa\SocialKit\Dto\TwitterTweet::class);
    }

    /**
     * Fetch a thread (conversationId, tweetCount, isThread, combinedText, author, tweets[]).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/twitter-thread-api
     *
     * @param ThreadRequest $req
     *
     * @return ApiResponse<TwitterThread>
     */
    public function thread(ThreadRequest $req): ApiResponse
    {
        return $this->postRequest('/twitter/thread', $req->toArray(), TwitterThread::class);
    }

    /**
     * Fetch a transcript for a tweet with a video attachment.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/twitter-transcript-api
     *
     * @param TranscriptRequest $req
     *
     * @return ApiResponse<Transcript>
     */
    public function transcript(TranscriptRequest $req): ApiResponse
    {
        return $this->postRequest('/twitter/transcript', $req->toArray(), Transcript::class);
    }
}
