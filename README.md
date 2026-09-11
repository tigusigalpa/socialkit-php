# SocialKit PHP SDK

> **Scrape social media content, transcripts, and stats from YouTube, TikTok, Instagram, Facebook, Twitter/X, and LinkedIn — right where your PHP code lives.**

`tigusigalpa/socialkit-php` is a modern, framework-agnostic PHP SDK for the
[SocialKit API](https://docs.socialkit.dev). It wraps every endpoint behind a
strongly-typed interface with immutable DTOs, secure key handling, automatic
error mapping, and first-class Laravel 10, 11, 12 & 13 integration.

```php
$transcript = SocialKit::youtube()->transcript(new TranscriptRequest(
    url: 'https://youtube.com/watch?v=dQw4w9WgXcQ',
));

echo $transcript->data->transcript;
```

[![Packagist Version](https://img.shields.io/packagist/v/tigusigalpa/socialkit-php.svg)](https://packagist.org/packages/tigusigalpa/socialkit-php)
[![PHP Version](https://img.shields.io/packagist/php-v/tigusigalpa/socialkit-php.svg)](https://packagist.org/packages/tigusigalpa/socialkit-php)
[![License](https://img.shields.io/packagist/l/tigusigalpa/socialkit-php.svg)](LICENSE)
[![Tests](https://img.shields.io/github/actions/workflow/status/tigusigalpa/socialkit-php/ci.yml?branch=main&label=tests)](https://github.com/tigusigalpa/socialkit-php/actions)

---

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
    - [Standalone](#standalone-framework-agnostic)
    - [Laravel](#laravel)
    - [Configuration Reference](#configuration-reference)
- [Quick Start](#quick-start)
- [Service Map](#service-map)
- [Method Usage](#method-usage)
    - [YouTube](#youtube)
    - [TikTok](#tiktok)
    - [Instagram](#instagram)
    - [Facebook](#facebook)
    - [Twitter/X](#twitterx)
    - [LinkedIn](#linkedin)
    - [Video](#video-direct-file-urls)
    - [Downloads (v2 Async)](#downloads-v2-async)
- [Custom Summary Response](#custom-summary-response)
- [Pagination](#pagination)
- [Transient Errors & Retries](#transient-errors--retries)
- [Credits Metadata](#credits-metadata)
- [V2 Async Jobs](#v2-async-jobs)
- [Laravel Integration](#laravel-integration)
- [Testing](#testing)
- [Compatibility](#compatibility)
- [Security](#security)
- [Endpoint Documentation](#endpoint-documentation)
- [Contributing](#contributing)
- [License](#license)

## Requirements

| Requirement          | Version                                                              |
|----------------------|----------------------------------------------------------------------|
| PHP                  | `8.1`, `8.2`, `8.3`, or `8.4`                                        |
| PSR-18 HTTP client   | Guzzle `^7.4` ships by default                                       |
| Laravel (optional)   | `10.x`, `11.x`, `12.x`, or `13.x`                                    |
| SocialKit access key | Get one from your [SocialKit dashboard](https://socialkit.dev)       |

## Installation

```bash
composer require tigusigalpa/socialkit-php
```

## Configuration

### Standalone (framework-agnostic)

```php
use Tigusigalpa\SocialKit\SocialKitClient;

$client = SocialKitClient::make('YOUR_ACCESS_KEY');
```

With custom options:

```php
use Tigusigalpa\SocialKit\SocialKitClient;
use Tigusigalpa\SocialKit\SocialKitConfig;

$config = new SocialKitConfig(
    accessKey: 'YOUR_ACCESS_KEY',
    baseUrl: 'https://api.socialkit.dev',
    timeout: 30.0,
    retryAttempts: 3,
    retryDelay: 1.0,
);

$client = new SocialKitClient($config);
```

From environment variables:

```php
$client = new SocialKitClient(SocialKitConfig::fromEnv());
```

### Laravel

Publish the config:

```bash
php artisan vendor:publish --tag=socialkit-config
```

Add to `.env`:

```env
SOCIALKIT_ACCESS_KEY=your-access-key
SOCIALKIT_BASE_URL=https://api.socialkit.dev
SOCIALKIT_TIMEOUT=30
SOCIALKIT_RETRY_ATTEMPTS=0
SOCIALKIT_RETRY_DELAY=1
```

### Configuration Reference

| Key (`config/socialkit.php`) | Env variable               | Default                       | Description                                    |
|------------------------------|----------------------------|-------------------------------|------------------------------------------------|
| `access_key`                 | `SOCIALKIT_ACCESS_KEY`     | `''`                          | Sent as `x-access-key` header.                 |
| `base_url`                   | `SOCIALKIT_BASE_URL`       | `https://api.socialkit.dev`   | API root URL.                                  |
| `timeout`                    | `SOCIALKIT_TIMEOUT`        | `30`                          | Per-request timeout in seconds.                |
| `retry_attempts`             | `SOCIALKIT_RETRY_ATTEMPTS` | `0`                           | Auto retries on 429/5xx (0 = no retries).      |
| `retry_delay`                | `SOCIALKIT_RETRY_DELAY`    | `1.0`                         | Base backoff delay in seconds.                 |
| `key_in_query`               | —                          | `false`                       | Compatibility: send key as query/body param.   |
| `user_agent`                 | —                          | `SocialKit-PHP-SDK/1.0.0`     | User-Agent header.                             |

## Quick Start

```php
use Tigusigalpa\SocialKit\SocialKitClient;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;

$client = SocialKitClient::make('YOUR_ACCESS_KEY');

// 1. Check API status (no key needed)
$status = $client->status()->status();
echo $status->data->overall; // "green"

// 2. Get YouTube video transcript
$transcript = $client->youtube()->transcript(new TranscriptRequest(
    url: 'https://youtube.com/watch?v=dQw4w9WgXcQ',
));

// 3. Get video stats
$stats = $client->youtube()->stats(new StatsRequest(
    url: 'https://youtube.com/watch?v=dQw4w9WgXcQ',
));
echo $stats->data->views; // 1000000

// 4. AI summary
$summary = $client->youtube()->summarize(new SummaryRequest(
    url: 'https://youtube.com/watch?v=dQw4w9WgXcQ',
));
echo $summary->data->summary;

// 5. Check credits
$credits = $client->status()->credits();
echo $credits->data->totalRemaining;
```

## Service Map

| Method                    | Service              | Endpoints                                |
|---------------------------|----------------------|------------------------------------------|
| `$client->status()`       | ServiceService       | status, credits                          |
| `$client->youtube()`      | YouTubeService       | transcript, summarize, stats, comments, channelStats, search, videos, download, bulk* |
| `$client->tiktok()`       | TikTokService        | transcript, summarize, stats, comments, channelStats, channelVideos, search, hashtagSearch, download, bulk* |
| `$client->instagram()`    | InstagramService     | transcript, summarize, stats, comments, channelStats, channelPosts, channelReels, reelsSearch, download, bulk* |
| `$client->facebook()`     | FacebookService      | transcript, summarize, stats, comments, channelStats |
| `$client->twitter()`      | TwitterService       | profile, tweets, tweet, thread, transcript |
| `$client->linkedin()`     | LinkedInService      | profile, company, companyPosts, post, transcript |
| `$client->video()`        | VideoService         | transcript, summarize (direct file URLs) |
| `$client->downloads()`    | DownloadsService     | start, get, wait (v2 async)              |

## Method Usage

### YouTube

```php
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\SummaryRequest;
use Tigusigalpa\SocialKit\Dto\StatsRequest;
use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\ChannelStatsRequest;
use Tigusigalpa\SocialKit\Dto\SearchRequest;
use Tigusigalpa\SocialKit\Dto\VideosRequest;
use Tigusigalpa\SocialKit\Dto\DownloadRequest;

// Transcript
$client->youtube()->transcript(new TranscriptRequest(url: '...'));

// AI Summary (with custom prompt)
$client->youtube()->summarize(new SummaryRequest(
    url: '...',
    customPrompt: 'Focus on technical details',
));

// Stats
$client->youtube()->stats(new StatsRequest(url: '...'));

// Comments (limit max 100, sortBy: top|new)
$client->youtube()->comments(new CommentsRequest(
    url: '...',
    limit: 50,
    sortBy: 'top',
));

// Channel stats
$client->youtube()->channelStats(new ChannelStatsRequest(url: '...'));

// Search (sortBy: relevance|date|views|rating, uploadDate: hour|today|week|month|year, type: video|shorts)
$client->youtube()->search(new SearchRequest(
    query: 'PHP tutorial',
    sortBy: 'relevance',
    uploadDate: 'month',
    type: 'video',
));

// Videos (fullDetails, capped at 30)
$client->youtube()->videos(new VideosRequest(
    url: '...',
    limit: 30,
    fullDetails: true,
));

// Download (max 10MB, URL expires 1h)
$client->youtube()->download(new DownloadRequest(
    url: '...',
    format: 'mp4',
    quality: '720p',
));
```

### TikTok

```php
use Tigusigalpa\SocialKit\Dto\ChannelVideosRequest;
use Tigusigalpa\SocialKit\Dto\HashtagSearchRequest;

// Channel videos (default 30, max 100, cursor + hasMore)
$client->tiktok()->channelVideos(new ChannelVideosRequest(
    url: 'https://tiktok.com/@user',
    limit: 30,
));

// Hashtag search (without #, max 100)
$client->tiktok()->hashtagSearch(new HashtagSearchRequest(
    hashtag: 'fyp',
    limit: 50,
));

// Download (max 30MB, URL expires 1h)
$client->tiktok()->download(new DownloadRequest(url: '...'));
```

### Instagram

```php
use Tigusigalpa\SocialKit\Dto\ChannelPostsRequest;
use Tigusigalpa\SocialKit\Dto\ChannelReelsRequest;
use Tigusigalpa\SocialKit\Dto\ReelsSearchRequest;

// Channel posts (cursor + hasMore, max 100, 1 credit/20 results)
$client->instagram()->channelPosts(new ChannelPostsRequest(
    url: 'https://instagram.com/test',
    limit: 20,
));

// Channel reels (cursor + hasMore, can take 60s)
$client->instagram()->channelReels(new ChannelReelsRequest(
    url: 'https://instagram.com/test',
));

// Reels search (page MUST be 1, hasMore always false)
$client->instagram()->reelsSearch(new ReelsSearchRequest(
    query: 'travel',
));
```

### Facebook

```php
// Stats (includes reactions[])
$stats = $client->facebook()->stats(new StatsRequest(url: '...'));
foreach ($stats->data->reactions as $reaction) {
    echo $reaction->name . ': ' . $reaction->count;
}

// No sync download, no bulk endpoints
```

### Twitter/X

```php
use Tigusigalpa\SocialKit\Dto\ProfileRequest;
use Tigusigalpa\SocialKit\Dto\TweetsRequest;
use Tigusigalpa\SocialKit\Dto\TweetRequest;
use Tigusigalpa\SocialKit\Dto\ThreadRequest;

// Profile
$client->twitter()->profile(new ProfileRequest(url: 'https://twitter.com/elonmusk'));

// Tweets (nextCursor nullable)
$tweets = $client->twitter()->tweets(new TweetsRequest(
    url: 'https://twitter.com/elonmusk',
    limit: 10,
));
// Use $tweets->data->nextCursor for next page

// Single tweet
$client->twitter()->tweet(new TweetRequest(url: '...'));

// Thread
$client->twitter()->thread(new ThreadRequest(url: '...'));
```

### LinkedIn

```php
use Tigusigalpa\SocialKit\Dto\CompanyPostsRequest;
use Tigusigalpa\SocialKit\Dto\PostRequest;

// Profile (with recentArticles[])
$client->linkedin()->profile(new ProfileRequest(url: '...'));

// Company
$client->linkedin()->company(new CompanyRequest(url: '...'));

// Company posts (max 50)
$client->linkedin()->companyPosts(new CompanyPostsRequest(
    url: '...',
    limit: 20,
));

// Single post
$client->linkedin()->post(new PostRequest(url: '...'));
```

### Video (Direct File URLs)

```php
use Tigusigalpa\SocialKit\Dto\VideoTranscriptRequest;
use Tigusigalpa\SocialKit\Dto\VideoSummaryRequest;

// 5 credits per started minute
$client->video()->transcript(new VideoTranscriptRequest(
    url: 'https://example.com/video.mp4',
));

$client->video()->summarize(new VideoSummaryRequest(
    url: 'https://example.com/video.mp4',
));
```

### Downloads (v2 Async)

```php
use Tigusigalpa\SocialKit\Dto\V2DownloadRequest;
use Tigusigalpa\SocialKit\Dto\WaitOptions;

// Start a download job
$job = $client->downloads()->start(new V2DownloadRequest(
    url: 'https://youtube.com/watch?v=test',
    platform: 'youtube',
    format: 'mp4',
    quality: '720p',
));

// Check status (ALWAYS GET)
$job = $client->downloads()->get($job->data->jobId);

// Wait for completion (caller-controlled polling)
$job = $client->downloads()->wait($job->data->jobId, new WaitOptions(
    interval: 2.0,
    timeout: 120.0,
));

if ($job->data->isReady()) {
    echo $job->data->downloadUrl;
}
```

## Custom Summary Response

You can provide a custom JSON schema for the summary response:

```php
$client->youtube()->summarize(new SummaryRequest(
    url: '...',
    customResponse: '{"sentiment": "string", "confidence": "number"}',
));

// Custom fields are available in $response->data->extra
echo $response->data->extra['sentiment'];
```

## Pagination

List endpoints use opaque cursor tokens. Pass the cursor from one response to
the next request:

```php
$response = $client->youtube()->comments(new CommentsRequest(
    url: '...',
    limit: 50,
));

if ($response->data->hasMore) {
    $next = $client->youtube()->comments(new CommentsRequest(
        url: '...',
        limit: 50,
        cursor: $response->data->cursor,
    ));
}
```

## Transient Errors & Retries

By default, the SDK does **not** retry. To enable conservative retries:

```php
$config = new SocialKitConfig(
    accessKey: '...',
    retryAttempts: 3,
    retryDelay: 1.0,
);
```

- 400, 401, 403, 404 are **never** retried.
- 429 and 5xx are retried with exponential backoff + jitter.
- The `Retry-After` header is always honored.
- POST retries require explicit opt-in (`retryAttempts > 0`).

## Credits Metadata

Every response includes credit and rate-limit metadata:

```php
$response = $client->youtube()->stats(new StatsRequest(url: '...'));

echo $response->meta->creditsUsed;
echo $response->meta->creditsRemaining;
echo $response->meta->rateLimitRemaining;
```

## V2 Async Jobs

V2 download jobs have four states: `queued` → `processing` → `ready` | `failed`.

The `wait()` method polls with caller-controlled interval and backoff, respects
timeouts, and never polls infinitely. No auto-resubmit on failure.

## Laravel Integration

### Facade

```php
use Tigusigalpa\SocialKit\Laravel\Facades\SocialKit;

$status = SocialKit::status()->status();
$transcript = SocialKit::youtube()->transcript(new TranscriptRequest(url: '...'));
```

### Dependency Injection

```php
use Tigusigalpa\SocialKit\SocialKitClientInterface;

class VideoController
{
    public function __construct(
        private readonly SocialKitClientInterface $socialkit,
    ) {}

    public function show()
    {
        return $this->socialkit->youtube()->stats(new StatsRequest(url: '...'));
    }
}
```

### Fake (Testing)

```php
use Tigusigalpa\SocialKit\Laravel\SocialKitFake;
use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\ResponseMeta;

$fake = new SocialKitFake();
$fake->queueData(['overall' => 'green', 'generated_at' => '2026-01-01']);

$response = $fake->request('/status', null, 'GET');
$fake->assertRequested('/status');
```

## Testing

```bash
composer install
composer test
```

All tests use Guzzle's `MockHandler` — no real API calls, no network required.

## Compatibility

- **Authentication:** `x-access-key` header by default. Set `keyInQuery: true`
  for the `access_key` query/body parameter compatibility mode.
- **Request strategy:** Protected scrape/read operations default to POST with
  JSON body (keeps key out of URL). GET is available via explicit method override.
- **Always GET:** `status()`, `credits()`, `downloads.get()`.

## Security

- The access key is **never** placed in URLs or query strings by default.
- All debug/error output replaces the key with `[REDACTED]`.
- The key is never logged, serialized, or exposed in exceptions, DTOs, or tests.
- HTTPS is used by default.

See [SECURITY.md](SECURITY.md) for the full policy.

## Endpoint Documentation

### Service & Async Downloads

- [Status API](https://docs.socialkit.dev/api-reference/status-api) — `GET /status`
- [Credits API](https://docs.socialkit.dev/api-reference/credits-api) — `GET /credits`
- [Async Download API v2](https://docs.socialkit.dev/api-reference/async-download-api) — `GET, POST /v2/{platform}/download`, `GET /v2/downloads/{jobId}`

### YouTube

- [Transcript](https://docs.socialkit.dev/api-reference/youtube-transcript-api) — `GET, POST /youtube/transcript`
- [Summary](https://docs.socialkit.dev/api-reference/youtube-summarize-api) — `GET, POST /youtube/summarize`
- [Stats](https://docs.socialkit.dev/api-reference/youtube-stats-api) — `GET, POST /youtube/stats`
- [Comments](https://docs.socialkit.dev/api-reference/youtube-comments-api) — `GET, POST /youtube/comments`
- [Channel Stats](https://docs.socialkit.dev/api-reference/youtube-channel-stats-api) — `GET, POST /youtube/channel-stats`
- [Search](https://docs.socialkit.dev/api-reference/youtube-search-api) — `GET, POST /youtube/search`
- [Videos](https://docs.socialkit.dev/api-reference/youtube-videos-api) — `GET, POST /youtube/videos`
- [Download](https://docs.socialkit.dev/api-reference/youtube-download-api) — `GET, POST /youtube/download`
- Bulk: `POST /youtube/transcript/bulk`, `POST /youtube/comments/bulk`, `POST /youtube/stats/bulk`, `POST /youtube/summarize/bulk` — [OpenAPI](https://api.socialkit.dev/openapi.json), [API Reference](https://docs.socialkit.dev/api-reference)

### TikTok

- [Transcript](https://docs.socialkit.dev/api-reference/tiktok-transcript-api) — `GET, POST /tiktok/transcript`
- [Summary](https://docs.socialkit.dev/api-reference/tiktok-summarize-api) — `GET, POST /tiktok/summarize`
- [Stats](https://docs.socialkit.dev/api-reference/tiktok-stats-api) — `GET, POST /tiktok/stats`
- [Comments](https://docs.socialkit.dev/api-reference/tiktok-comments-api) — `GET, POST /tiktok/comments`
- [Channel Stats](https://docs.socialkit.dev/api-reference/tiktok-channel-stats-api) — `GET, POST /tiktok/channel-stats`
- [Channel Videos](https://docs.socialkit.dev/api-reference/tiktok-channel-videos-api) — `GET, POST /tiktok/channel-videos`
- [Search](https://docs.socialkit.dev/api-reference/tiktok-search-api) — `GET, POST /tiktok/search`
- [Hashtag Search](https://docs.socialkit.dev/api-reference/tiktok-hashtag-search-api) — `GET, POST /tiktok/hashtag-search`
- [Download](https://docs.socialkit.dev/api-reference/tiktok-download-api) — `GET, POST /tiktok/download`
- Bulk: `POST /tiktok/transcript/bulk`, `POST /tiktok/comments/bulk`, `POST /tiktok/stats/bulk`, `POST /tiktok/channel-stats/bulk`, `POST /tiktok/summarize/bulk` — [OpenAPI](https://api.socialkit.dev/openapi.json), [API Reference](https://docs.socialkit.dev/api-reference)

### Instagram

- [Transcript](https://docs.socialkit.dev/api-reference/instagram-transcript-api) — `GET, POST /instagram/transcript`
- [Summary](https://docs.socialkit.dev/api-reference/instagram-summarize-api) — `GET, POST /instagram/summarize`
- [Stats](https://docs.socialkit.dev/api-reference/instagram-stats-api) — `GET, POST /instagram/stats`
- [Comments](https://docs.socialkit.dev/api-reference/instagram-comments-api) — `GET, POST /instagram/comments`
- [Channel Stats](https://docs.socialkit.dev/api-reference/instagram-channel-stats-api) — `GET, POST /instagram/channel-stats`
- [Channel Posts](https://docs.socialkit.dev/api-reference/instagram-channel-posts-api) — `GET, POST /instagram/channel-posts`
- [Channel Reels](https://docs.socialkit.dev/api-reference/instagram-channel-reels-api) — `GET, POST /instagram/channel-reels`
- [Reels Search](https://docs.socialkit.dev/api-reference/instagram-reels-search-api) — `GET, POST /instagram/reels-search`
- [Download](https://docs.socialkit.dev/api-reference/instagram-download-api) — `GET, POST /instagram/download`
- Bulk: `POST /instagram/transcript/bulk`, `POST /instagram/stats/bulk`, `POST /instagram/channel-stats/bulk`, `POST /instagram/summarize/bulk` — [OpenAPI](https://api.socialkit.dev/openapi.json), [API Reference](https://docs.socialkit.dev/api-reference)

### Facebook

- [Transcript](https://docs.socialkit.dev/api-reference/facebook-transcript-api) — `GET, POST /facebook/transcript`
- [Summary](https://docs.socialkit.dev/api-reference/facebook-summarize-api) — `GET, POST /facebook/summarize`
- [Stats](https://docs.socialkit.dev/api-reference/facebook-stats-api) — `GET, POST /facebook/stats`
- [Comments](https://docs.socialkit.dev/api-reference/facebook-comments-api) — `GET, POST /facebook/comments`
- [Channel Stats](https://docs.socialkit.dev/api-reference/facebook-channel-stats-api) — `GET, POST /facebook/channel-stats`

### Twitter/X

- [Profile](https://docs.socialkit.dev/api-reference/twitter-profile-api) — `GET, POST /twitter/profile`
- [Tweets](https://docs.socialkit.dev/api-reference/twitter-tweets-api) — `GET, POST /twitter/tweets`
- [Tweet](https://docs.socialkit.dev/api-reference/twitter-tweet-api) — `GET, POST /twitter/tweet`
- [Thread](https://docs.socialkit.dev/api-reference/twitter-thread-api) — `GET, POST /twitter/thread`
- [Transcript](https://docs.socialkit.dev/api-reference/twitter-transcript-api) — `GET, POST /twitter/transcript`

### LinkedIn

- [Profile](https://docs.socialkit.dev/api-reference/linkedin-profile-api) — `GET, POST /linkedin/profile`
- [Company](https://docs.socialkit.dev/api-reference/linkedin-company-api) — `GET, POST /linkedin/company`
- [Company Posts](https://docs.socialkit.dev/api-reference/linkedin-company-posts-api) — `GET, POST /linkedin/company-posts`
- [Post](https://docs.socialkit.dev/api-reference/linkedin-post-api) — `GET, POST /linkedin/post`
- [Transcript](https://docs.socialkit.dev/api-reference/linkedin-transcript-api) — `GET, POST /linkedin/transcript`

### Direct Video

- [Transcript](https://docs.socialkit.dev/api-reference/video-transcript-api) — `GET, POST /video/transcript`
- [Summary](https://docs.socialkit.dev/api-reference/video-summarize-api) — `GET, POST /video/summarize`

### Guides

- [Getting Started](https://docs.socialkit.dev/getting-started)
- [Authentication](https://docs.socialkit.dev/authentication)
- [API Reference](https://docs.socialkit.dev/api-reference)
- [Errors](https://docs.socialkit.dev/api-reference/errors)
- [OpenAPI 3.0 Contract](https://api.socialkit.dev/openapi.json)

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## License

Released under the [MIT License](LICENSE).

## Credits

Built and maintained by [Igor Sazonov](https://github.com/tigusigalpa) — [sovletig@gmail.com](mailto:sovletig@gmail.com).
