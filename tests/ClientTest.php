<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tigusigalpa\SocialKit\SocialKitClient;
use Tigusigalpa\SocialKit\SocialKitConfig;

final class ClientTest extends TestCase
{
    public function testMakeFactoryCreatesClient(): void
    {
        $client = SocialKitClient::make('my-key');

        self::assertInstanceOf(SocialKitClient::class, $client);
        self::assertSame('my-key', $client->getConfig()->accessKey);
    }

    public function testMakeFactoryWithOptions(): void
    {
        $client = SocialKitClient::make('my-key', [
            'base_url' => 'https://custom.api.dev',
            'timeout' => 60.0,
            'retry_attempts' => 5,
        ]);

        self::assertSame('https://custom.api.dev', $client->getConfig()->baseUrl);
        self::assertSame(60.0, $client->getConfig()->timeout);
        self::assertSame(5, $client->getConfig()->retryAttempts);
    }

    public function testConfigFromArray(): void
    {
        $config = SocialKitConfig::fromArray([
            'access_key' => 'key123',
            'base_url' => 'https://test.api.dev/',
            'timeout' => 45.0,
            'retry_attempts' => 3,
            'retry_delay' => 2.0,
            'key_in_query' => true,
        ]);

        self::assertSame('key123', $config->accessKey);
        self::assertSame('https://test.api.dev', $config->baseUrl);
        self::assertSame(45.0, $config->timeout);
        self::assertSame(3, $config->retryAttempts);
        self::assertSame(2.0, $config->retryDelay);
        self::assertTrue($config->keyInQuery);
    }

    public function testConfigDefaults(): void
    {
        $config = new SocialKitConfig(accessKey: 'key');

        self::assertSame('https://api.socialkit.dev', $config->baseUrl);
        self::assertSame(30.0, $config->timeout);
        self::assertSame(0, $config->retryAttempts);
        self::assertSame(1.0, $config->retryDelay);
        self::assertFalse($config->keyInQuery);
        self::assertSame('SocialKit-PHP-SDK/1.0.0', $config->userAgent);
    }

    public function testRedactKey(): void
    {
        $client = $this->makeClient([]);
        $redacted = $client->redactKey('some text with test-key-12345 in it');

        self::assertSame('some text with [REDACTED] in it', $redacted);
    }

    public function testRedactKeyWithEmptyKey(): void
    {
        $config = new SocialKitConfig(accessKey: '');
        $client = new SocialKitClient($config);
        $redacted = $client->redactKey('some text');

        self::assertSame('some text', $redacted);
    }

    public function testPostRequestSendsJsonBodyWithAccessKeyHeader(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['url' => 'https://youtube.com/watch?v=test']]),
        ]);

        $response = $client->request('/youtube/transcript', ['url' => 'https://youtube.com/watch?v=test']);

        self::assertTrue($response->success);
        self::assertSame(200, $response->meta->httpStatus);

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/youtube/transcript', $request->getUri()->getPath());
        self::assertSame('test-key-12345', $request->getHeaderLine('x-access-key'));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));

        $body = json_decode((string) $request->getBody(), true);
        self::assertSame('https://youtube.com/watch?v=test', $body['url']);
    }

    public function testGetRequestSendsQueryParams(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['overall' => 'green']]),
        ]);

        $client->request('/status', null, 'GET');

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/status', $request->getUri()->getPath());
    }

    public function testStatusIsAlwaysGet(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['overall' => 'green']]),
        ]);

        $client->request('/status', ['some' => 'data'], 'POST');

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
    }

    public function testCreditsIsAlwaysGet(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['total_remaining' => 100.0]]),
        ]);

        $client->request('/credits', ['some' => 'data'], 'POST');

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
    }

    public function testDownloadsGetIsAlwaysGet(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['job_id' => 'abc', 'status' => 'ready']]),
        ]);

        $client->request('/v2/downloads/abc-123', ['some' => 'data'], 'POST');

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
    }

    public function testKeyInQueryMode(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => []]),
        ], keyInQuery: true);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);

        $request = $this->lastRequest();
        // Key should NOT be in header when keyInQuery is true
        self::assertSame('', $request->getHeaderLine('x-access-key'));

        // Key should be in the JSON body
        $body = json_decode((string) $request->getBody(), true);
        self::assertSame('test-key-12345', $body['access_key']);
    }

    public function testKeyInQueryModeForGet(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['overall' => 'green']]),
        ], keyInQuery: true);

        $client->request('/status', null, 'GET');

        $request = $this->lastRequest();
        $query = $request->getUri()->getQuery();
        self::assertStringContainsString('access_key=test-key-12345', $query);
    }

    public function testResponseMetaHeaders(): void
    {
        $client = $this->makeClient([
            new Response(200, [
                'Content-Type' => 'application/json',
                'X-Credits-Used' => '1.0',
                'X-Credits-Remaining' => '99.0',
                'X-RateLimit-Limit' => '100',
                'X-RateLimit-Remaining' => '95',
                'X-RateLimit-Reset' => '1700000000',
            ], json_encode(['success' => true, 'data' => []])),
        ]);

        $response = $client->request('/youtube/stats', ['url' => 'https://test.com']);

        self::assertSame(1.0, $response->meta->creditsUsed);
        self::assertSame(99.0, $response->meta->creditsRemaining);
        self::assertSame(100, $response->meta->rateLimitLimit);
        self::assertSame(95, $response->meta->rateLimitRemaining);
        self::assertSame(1700000000, $response->meta->rateLimitReset);
    }

    public function testUserAgentHeader(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => []]),
        ]);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);

        $request = $this->lastRequest();
        self::assertSame('SocialKit-PHP-SDK/1.0.0', $request->getHeaderLine('User-Agent'));
    }
}
