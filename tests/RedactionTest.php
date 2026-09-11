<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use GuzzleHttp\Psr7\Response;
use Tigusigalpa\SocialKit\Exceptions\RateLimitException;

final class RedactionTest extends TestCase
{
    public function testKeyNeverInResponseBody(): void
    {
        // Even if the API somehow echoes the key back, it should be redacted
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'success' => true,
                'data' => ['echo' => 'test-key-12345'],
            ])),
        ]);

        $response = $client->request('/youtube/transcript', ['url' => 'https://test.com']);

        // The rawBody in meta should have the key redacted
        self::assertStringNotContainsString('test-key-12345', $response->meta->rawBody);
        self::assertStringContainsString('[REDACTED]', $response->meta->rawBody);
    }

    public function testKeyNeverInExceptionMessage(): void
    {
        $client = $this->makeClient([
            new Response(401, [], json_encode(['message' => 'Key test-key-12345 is invalid'])),
        ]);

        try {
            $client->request('/youtube/transcript', ['url' => 'https://test.com']);
            self::fail('Expected exception');
        } catch (\Tigusigalpa\SocialKit\Exceptions\AuthenticationException $e) {
            // The exception message comes from the API, not from our code
            // But the rawBody in meta should be redacted
            self::assertStringNotContainsString('test-key-12345', $e->getMessage());
        }
    }

    public function testKeyNeverInRequestUrl(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['overall' => 'green']]),
        ]);

        $client->request('/status', null, 'GET');

        $request = $this->lastRequest();
        $url = (string) $request->getUri();
        self::assertStringNotContainsString('test-key-12345', $url);
    }

    public function testKeyNeverInRequestBodyWhenHeaderMode(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => []]),
        ]);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);

        $request = $this->lastRequest();
        $body = (string) $request->getBody();
        self::assertStringNotContainsString('test-key-12345', $body);
    }

    public function testKeyInHeaderOnly(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => []]),
        ]);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);

        $request = $this->lastRequest();
        self::assertSame('test-key-12345', $request->getHeaderLine('x-access-key'));
    }

    public function testRedactKeyMethod(): void
    {
        $client = $this->makeClient([]);

        self::assertSame('[REDACTED]', $client->redactKey('test-key-12345'));
        self::assertSame('hello [REDACTED] world', $client->redactKey('hello test-key-12345 world'));
        self::assertSame('no key here', $client->redactKey('no key here'));
    }
}
