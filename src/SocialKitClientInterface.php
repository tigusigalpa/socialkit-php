<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit;

use Tigusigalpa\SocialKit\Services\DownloadsService;
use Tigusigalpa\SocialKit\Services\FacebookService;
use Tigusigalpa\SocialKit\Services\InstagramService;
use Tigusigalpa\SocialKit\Services\LinkedInService;
use Tigusigalpa\SocialKit\Services\ServiceService;
use Tigusigalpa\SocialKit\Services\TikTokService;
use Tigusigalpa\SocialKit\Services\TwitterService;
use Tigusigalpa\SocialKit\Services\VideoService;
use Tigusigalpa\SocialKit\Services\YouTubeService;

/**
 * Contract for the SocialKit API client.
 *
 * Defines the service accessor methods and the low-level request method
 * implemented by {@see SocialKitClient} and {@see \Tigusigalpa\SocialKit\Laravel\SocialKitFake}.
 */
interface SocialKitClientInterface
{
    /**
     * Send a low-level HTTP request to the SocialKit API and return a typed response.
     *
     * @param string                    $path      API path, e.g. `/youtube/transcript`.
     * @param array<string, mixed>|null $body      JSON body for POST requests, or null for GET.
     * @param string                    $method    HTTP method override ('GET' or 'POST'). Defaults to POST for protected endpoints.
     * @param array<string, mixed>      $query     Query string parameters (for GET requests).
     *
     * @return ApiResponse<mixed>
     *
     * @throws \Tigusigalpa\SocialKit\Exceptions\SocialKitException
     */
    public function request(
        string $path,
        ?array $body = null,
        string $method = 'POST',
        array $query = [],
    ): ApiResponse;

    /** Access the Service (status & credits) endpoint group. */
    public function status(): ServiceService;

    /** Access the YouTube endpoint group. */
    public function youtube(): YouTubeService;

    /** Access the TikTok endpoint group. */
    public function tiktok(): TikTokService;

    /** Access the Instagram endpoint group. */
    public function instagram(): InstagramService;

    /** Access the Facebook endpoint group. */
    public function facebook(): FacebookService;

    /** Access the Twitter endpoint group. */
    public function twitter(): TwitterService;

    /** Access the LinkedIn endpoint group. */
    public function linkedin(): LinkedInService;

    /** Access the Video endpoint group. */
    public function video(): VideoService;

    /** Access the Downloads (v2 async) endpoint group. */
    public function downloads(): DownloadsService;
}
