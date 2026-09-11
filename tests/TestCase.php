<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Tigusigalpa\SocialKit\SocialKitClient;
use Tigusigalpa\SocialKit\SocialKitConfig;

/**
 * Base test case providing a mock HTTP client builder.
 */
abstract class TestCase extends BaseTestCase
{
    /** @var array<int, array<string, mixed>> */
    protected array $history = [];

    /**
     * Build a SocialKitClient with a mocked Guzzle HTTP client.
     *
     * @param list<Response|\Throwable> $responses
     */
    protected function makeClient(array $responses, int $retryAttempts = 0, float $retryDelay = 0.0, bool $keyInQuery = false): SocialKitClient
    {
        $this->history = [];

        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($this->history));

        $guzzle = new GuzzleClient(['handler' => $stack]);
        $config = new SocialKitConfig(
            accessKey: 'test-key-12345',
            retryAttempts: $retryAttempts,
            retryDelay: $retryDelay,
            keyInQuery: $keyInQuery,
        );

        return new SocialKitClient($config, $guzzle);
    }

    /**
     * Create a JSON response.
     */
    protected function json(array $data, int $status = 200, array $headers = []): Response
    {
        return new Response(
            $status,
            array_merge(['Content-Type' => 'application/json'], $headers),
            json_encode($data, JSON_THROW_ON_ERROR) ?: '',
        );
    }

    /**
     * Get the last recorded request.
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function lastRequest(): \Psr\Http\Message\RequestInterface
    {
        return $this->history[0]['request'];
    }
}
