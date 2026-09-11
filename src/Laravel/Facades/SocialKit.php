<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Tigusigalpa\SocialKit\Laravel\SocialKitFake;
use Tigusigalpa\SocialKit\SocialKitClientInterface;
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
 * Laravel facade exposing the SocialKit API.
 *
 * @method static ServiceService   status()
 * @method static ServiceService   credits()
 * @method static YouTubeService   youtube()
 * @method static TikTokService    tiktok()
 * @method static InstagramService instagram()
 * @method static FacebookService  facebook()
 * @method static TwitterService   twitter()
 * @method static LinkedInService  linkedin()
 * @method static VideoService     video()
 * @method static DownloadsService downloads()
 * @method static \Tigusigalpa\SocialKit\ApiResponse request(string $path, ?array $body = null, string $method = 'POST', array $query = [])
 *
 * @see SocialKitClientInterface
 */
final class SocialKit extends Facade
{
    /**
     * Replace the resolved client with a fake for the duration of a test.
     */
    public static function fake(?SocialKitFake $fake = null): SocialKitFake
    {
        $fake ??= new SocialKitFake();

        static::swap($fake);

        return $fake;
    }

    /**
     * Get the registered name of the component in the service container.
     */
    protected static function getFacadeAccessor(): string
    {
        return SocialKitClientInterface::class;
    }
}
