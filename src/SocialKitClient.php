<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\HttpFactory;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Tigusigalpa\SocialKit\Exceptions\AsyncJobFailedException;
use Tigusigalpa\SocialKit\Exceptions\AuthenticationException;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;
use Tigusigalpa\SocialKit\Exceptions\DecodeException;
use Tigusigalpa\SocialKit\Exceptions\ForbiddenException;
use Tigusigalpa\SocialKit\Exceptions\InsufficientCreditsException;
use Tigusigalpa\SocialKit\Exceptions\NotFoundException;
use Tigusigalpa\SocialKit\Exceptions\RateLimitException;
use Tigusigalpa\SocialKit\Exceptions\ServerException;
use Tigusigalpa\SocialKit\Exceptions\SocialKitException;
use Tigusigalpa\SocialKit\Exceptions\TimeoutException;
use Tigusigalpa\SocialKit\Exceptions\TransportException;
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
 * Framework-agnostic HTTP client for the SocialKit API.
 *
 * Handles request signing (x-access-key header authentication), JSON
 * encoding/decoding, automatic exponential-backoff retries on transient
 * errors, and mapping of non-2xx responses to the SDK's exception hierarchy.
 *
 * Any PSR-18 compatible HTTP client may be injected via the constructor;
 * Guzzle is used by default.
 */
final class SocialKitClient implements SocialKitClientInterface
{
    private ClientInterface $httpClient;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    private UriFactoryInterface $uriFactory;

    /** @var list<string> */
    private const GET_ONLY_PATHS = [
        '/status',
        '/credits',
    ];

    /**
     * @param SocialKitConfig             $config         SDK configuration (access key, base URL, retry policy, etc.).
     * @param ClientInterface|null        $httpClient     Any PSR-18 compatible HTTP client. Defaults to Guzzle.
     * @param RequestFactoryInterface|null $requestFactory PSR-17 request factory. Defaults to Guzzle's HttpFactory.
     * @param StreamFactoryInterface|null $streamFactory  PSR-17 stream factory. Defaults to Guzzle's HttpFactory.
     * @param UriFactoryInterface|null    $uriFactory     PSR-17 URI factory. Defaults to Guzzle's HttpFactory.
     */
    public function __construct(
        private readonly SocialKitConfig $config,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?UriFactoryInterface $uriFactory = null,
    ) {
        $factory = new HttpFactory();

        $this->httpClient = $httpClient ?? new GuzzleClient(['timeout' => $config->timeout]);
        $this->requestFactory = $requestFactory ?? $factory;
        $this->streamFactory = $streamFactory ?? $factory;
        $this->uriFactory = $uriFactory ?? $factory;
    }

    /**
     * Convenience factory for standalone (non-Laravel) usage.
     *
     * @param string               $accessKey SocialKit API access key.
     * @param array<string, mixed> $options   Optional overrides: base_url, timeout, retry_attempts, retry_delay,
     *                                        user_agent, default_headers, key_in_query.
     */
    public static function make(string $accessKey, array $options = []): self
    {
        return new self(SocialKitConfig::fromArray(['access_key' => $accessKey] + $options));
    }

    /** Access the Service (status & credits) endpoint group. */
    public function status(): ServiceService
    {
        return new ServiceService($this);
    }

    /** Access the YouTube endpoint group. */
    public function youtube(): YouTubeService
    {
        return new YouTubeService($this);
    }

    /** Access the TikTok endpoint group. */
    public function tiktok(): TikTokService
    {
        return new TikTokService($this);
    }

    /** Access the Instagram endpoint group. */
    public function instagram(): InstagramService
    {
        return new InstagramService($this);
    }

    /** Access the Facebook endpoint group. */
    public function facebook(): FacebookService
    {
        return new FacebookService($this);
    }

    /** Access the Twitter endpoint group. */
    public function twitter(): TwitterService
    {
        return new TwitterService($this);
    }

    /** Access the LinkedIn endpoint group. */
    public function linkedin(): LinkedInService
    {
        return new LinkedInService($this);
    }

    /** Access the Video endpoint group. */
    public function video(): VideoService
    {
        return new VideoService($this);
    }

    /** Access the Downloads (v2 async) endpoint group. */
    public function downloads(): DownloadsService
    {
        return new DownloadsService($this);
    }

    /**
     * Get the SDK configuration.
     */
    public function getConfig(): SocialKitConfig
    {
        return $this->config;
    }

    /**
     * Redact the access key from a string, replacing it with [REDACTED].
     *
     * @param string $value
     */
    public function redactKey(string $value): string
    {
        if ($this->config->accessKey === '') {
            return $value;
        }

        return str_replace($this->config->accessKey, '[REDACTED]', $value);
    }

    /**
     * Send a low-level HTTP request to the SocialKit API and return a typed response.
     *
     * @param string                    $path   API path, e.g. `/youtube/transcript`.
     * @param array<string, mixed>|null $body   JSON body for POST requests, or null for GET.
     * @param string                    $method HTTP method override ('GET' or 'POST'). Defaults to POST for protected endpoints.
     * @param array<string, mixed>      $query  Query string parameters (for GET requests).
     *
     * @return ApiResponse<mixed>
     *
     * @throws SocialKitException
     */
    public function request(
        string $path,
        ?array $body = null,
        string $method = 'POST',
        array $query = [],
    ): ApiResponse {
        $isGetOnly = $this->isGetOnlyPath($path);
        $method = strtoupper($method);

        if ($isGetOnly) {
            $method = 'GET';
            $body = null;
        }

        if ($method === 'GET') {
            $body = null;
        }

        $attempt = 0;
        $maxAttempts = max(0, $this->config->retryAttempts);

        while (true) {
            try {
                return $this->send($method, $path, $body, $query);
            } catch (RateLimitException | ServerException $e) {
                if ($attempt >= $maxAttempts) {
                    throw $e;
                }

                // POST retry must be explicit opt-in via retryAttempts > 0
                if ($method === 'POST' && $this->config->retryAttempts === 0) {
                    throw $e;
                }

                $delay = $e instanceof RateLimitException
                    ? ($e->retryAfter ?? (int) ($this->config->retryDelay * (2 ** $attempt)))
                    : (int) ($this->config->retryDelay * (2 ** $attempt));

                // Add jitter (0-25% of delay)
                $jitter = (int) ($delay * 0.25 * (mt_rand(0, 1000) / 1000));
                $totalDelay = $delay + $jitter;

                if ($totalDelay > 0) {
                    usleep((int) ($totalDelay * 1_000_000));
                }

                $attempt++;
            }
        }
    }

    /**
     * @param array<string, mixed>|null $body
     * @param array<string, mixed>      $query
     *
     * @return ApiResponse<mixed>
     *
     * @throws SocialKitException
     */
    private function send(string $method, string $path, ?array $body, array $query): ApiResponse
    {
        $uri = $this->uriFactory
            ->createUri(rtrim($this->config->baseUrl, '/') . '/' . ltrim($path, '/'));

        // For GET requests, merge query params
        $filteredQuery = array_filter($query, static fn ($value) => $value !== null);
        if ($method === 'GET' && $filteredQuery !== []) {
            $uri = $uri->withQuery(http_build_query($filteredQuery, '', '&', PHP_QUERY_RFC3986));
        }

        $request = $this->requestFactory
            ->createRequest($method, $uri)
            ->withHeader('Accept', 'application/json')
            ->withHeader('User-Agent', $this->config->userAgent);

        // Merge default headers
        foreach ($this->config->defaultHeaders as $name => $value) {
            $request = $request->withHeader($name, (string) $value);
        }

        // Authentication: header by default, query/body if keyInQuery is true
        if ($this->config->accessKey !== '') {
            if ($this->config->keyInQuery) {
                if ($method === 'GET') {
                    $existingQuery = $uri->getQuery();
                    $keyParam = http_build_query(['access_key' => $this->config->accessKey], '', '&', PHP_QUERY_RFC3986);
                    $uri = $uri->withQuery($existingQuery !== '' ? $existingQuery . '&' . $keyParam : $keyParam);
                    $request = $request->withUri($uri);
                } elseif ($body !== null) {
                    $body['access_key'] = $this->config->accessKey;
                }
            } else {
                $request = $request->withHeader('x-access-key', $this->config->accessKey);
            }
        }

        // For POST requests with a body, encode JSON
        if ($method === 'POST' && $body !== null) {
            try {
                $json = json_encode($body, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new DecodeException('Failed to encode request body: ' . $e->getMessage(), $e);
            }
            $stream = $this->streamFactory->createStream($json);
            $request = $request->withBody($stream)
                ->withHeader('Content-Type', 'application/json');
        }

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ConnectException $e) {
            if (str_contains(strtolower($e->getMessage()), 'timeout')) {
                throw new TimeoutException('SocialKit API request timed out: ' . $e->getMessage(), $e);
            }
            throw new TransportException('SocialKit API transport error: ' . $e->getMessage(), $e);
        } catch (ClientExceptionInterface $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof ConnectException) {
                if (str_contains(strtolower($previous->getMessage()), 'timeout')) {
                    throw new TimeoutException('SocialKit API request timed out: ' . $previous->getMessage(), $previous);
                }
                throw new TransportException('SocialKit API transport error: ' . $previous->getMessage(), $previous);
            }
            throw new TransportException('SocialKit API request failed: ' . $e->getMessage(), $e);
        }

        $statusCode = $response->getStatusCode();
        $rawBody = (string) $response->getBody();
        $redactedBody = $this->redactKey($rawBody);

        $body = [];
        if ($rawBody !== '') {
            try {
                /** @var array<string, mixed> $body */
                $body = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                if ($statusCode >= 200 && $statusCode < 300) {
                    throw new DecodeException('Failed to decode SocialKit API response: ' . $e->getMessage(), $e);
                }
                $body = ['message' => $redactedBody];
            }
        }

        $meta = ResponseMeta::fromResponse($response, $redactedBody);

        if ($statusCode >= 200 && $statusCode < 300) {
            $success = (bool) ($body['success'] ?? true);
            $data = $body['data'] ?? $body;

            return new ApiResponse($data, $meta, $success);
        }

        // Redact the key from the body before passing to exception mapping
        $redactedBodyArray = $this->redactKeyFromArray($body);

        throw $this->mapException($statusCode, $redactedBodyArray, $meta);
    }

    /**
     * @param array<string, mixed> $body
     */
    private function mapException(int $statusCode, array $body, ResponseMeta $meta): SocialKitException
    {
        // Check for insufficient_credits code on 403
        if ($statusCode === 403 && (($body['code'] ?? null) === 'insufficient_credits')) {
            return InsufficientCreditsException::fromResponse($statusCode, $body, $meta);
        }

        return match ($statusCode) {
            400 => BadRequestException::fromResponse($statusCode, $body, $meta),
            401 => AuthenticationException::fromResponse($statusCode, $body, $meta),
            403 => ForbiddenException::fromResponse($statusCode, $body, $meta),
            404 => NotFoundException::fromResponse($statusCode, $body, $meta),
            429 => RateLimitException::fromResponse($statusCode, $body, $meta),
            500, 502, 503, 504 => ServerException::fromResponse($statusCode, $body, $meta),
            default => ServerException::fromResponse($statusCode, $body, $meta),
        };
    }

    /**
     * Recursively redact the access key from all string values in an array.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function redactKeyFromArray(array $data): array
    {
        if ($this->config->accessKey === '') {
            return $data;
        }

        array_walk_recursive($data, function (mixed &$value): void {
            if (is_string($value)) {
                $value = $this->redactKey($value);
            }
        });

        return $data;
    }

    private function isGetOnlyPath(string $path): bool
    {
        $path = '/' . ltrim($path, '/');

        foreach (self::GET_ONLY_PATHS as $getOnly) {
            if ($path === $getOnly) {
                return true;
            }
        }

        // downloads.get is always GET: /v2/downloads/{jobId}
        if (preg_match('#^/v2/downloads/[^/]+$#', $path)) {
            return true;
        }

        return false;
    }
}
