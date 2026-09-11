# Changelog

All notable changes to `tigusigalpa/socialkit-php` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed

- Made `SocialKit::fake()` functional for all service accessors, so service calls
  are queued and recorded instead of creating a real HTTP client.
- Retried transport failures when retries are enabled and redact the access key
  from transport error messages.
- Bound retained non-2xx response bodies to 1 MiB, preventing oversized upstream
  error pages from consuming unbounded memory.
- Escaped asynchronous download job IDs as URL path segments and reject empty IDs.

### Changed

- Compatibility authentication now keeps the `x-access-key` header while also
  adding `access_key` to the query/body, matching the Go SDK.
- Added validation for unsafe download polling options and support for
  `SOCIALKIT_USER_AGENT` and `SOCIALKIT_KEY_IN_QUERY` in `SocialKitConfig::fromEnv()`.
- Added GitHub Actions workflows for the PHP test matrix, Codecov coverage, and
  scheduled CodeQL security analysis.

## [1.0.0] - 2026-01-01

### Added

- Initial release of the SocialKit API PHP SDK.
- Framework-agnostic `SocialKitClient` with PSR-18 HTTP client support (Guzzle by default).
- Immutable `SocialKitConfig` with `fromArray()` and `fromEnv()` factories.
- Secure authentication via `x-access-key` header by default; optional `key_in_query`
  compatibility mode.
- Key redaction in all debug/error output via `redactKey()`.
- Request strategy: POST + JSON body for protected scrape/read operations by default;
  GET for `status()`, `credits()`, and `downloads.get()`.
- Generic `ApiResponse<T>` wrapper with `ResponseMeta` (credits, rate-limit, retry headers).
- Comprehensive exception hierarchy: `SocialKitException` (abstract base),
  `BadRequestException`, `AuthenticationException`, `ForbiddenException`,
  `InsufficientCreditsException`, `NotFoundException`, `RateLimitException`,
  `ServerException`, `TransportException`, `TimeoutException`, `DecodeException`,
  `AsyncJobFailedException`.
- Immutable request/option DTOs for all endpoint types.
- Typed response DTOs for all response data.
- Service classes: `ServiceService`, `YouTubeService`, `TikTokService`,
  `InstagramService`, `FacebookService`, `TwitterService`, `LinkedInService`,
  `VideoService`, `DownloadsService`.
- YouTube: transcript, summarize, stats, comments, channelStats, search, videos,
  download, and bulk (experimental) methods.
- TikTok: transcript, summarize, stats, comments, channelStats, channelVideos,
  search, hashtagSearch, download, and bulk (experimental) methods.
- Instagram: transcript, summarize, stats, comments, channelStats, channelPosts,
  channelReels, reelsSearch (page=1 validation), download, and bulk (experimental) methods.
- Facebook: transcript, summarize, stats (with reactions[]), comments, channelStats.
- Twitter: profile, tweets (nextCursor), tweet, thread, transcript.
- LinkedIn: profile (with recentArticles[]), company, companyPosts (max 50),
  post, transcript.
- Video: transcript, summarize (direct video file URLs, 5 credits/started minute).
- Downloads: v2 async start, get (ALWAYS GET), wait (caller-controlled polling).
- Conservative retry logic: default 0 (no retries), opt-in via config; exponential
  backoff with jitter; honors Retry-After; POST retry requires explicit opt-in.
- Laravel 10/11/12/13 integration: `SocialKitServiceProvider`, `SocialKit` facade,
  `SocialKitFake` test helper, and a publishable `config/socialkit.php`.
- PHPUnit 10/11/12 test suite covering all services, exceptions, redaction,
  pagination, bulk methods, and Laravel integration.
