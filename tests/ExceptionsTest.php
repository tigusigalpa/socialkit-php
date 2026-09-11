<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use GuzzleHttp\Psr7\Response;
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

final class ExceptionsTest extends TestCase
{
    public function testErrorResponseBodyIsBounded(): void
    {
        $client = $this->makeClient([
            new Response(500, [], str_repeat('x', 1_048_577)),
        ]);

        try {
            $client->request('/youtube/transcript', ['url' => 'https://test.com']);
            self::fail('Expected ServerException');
        } catch (ServerException $e) {
            self::assertNotNull($e->getMeta());
            self::assertSame(1_048_576, strlen($e->getMeta()->rawBody));
        }
    }

    public function testBadRequestException(): void
    {
        $client = $this->makeClient([
            new Response(400, [], json_encode(['message' => 'Invalid URL'])),
        ]);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid URL');

        $client->request('/youtube/transcript', ['url' => '']);
    }

    public function testAuthenticationException(): void
    {
        $client = $this->makeClient([
            new Response(401, [], json_encode(['message' => 'Invalid access key'])),
        ]);

        $this->expectException(AuthenticationException::class);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);
    }

    public function testForbiddenException(): void
    {
        $client = $this->makeClient([
            new Response(403, [], json_encode(['message' => 'Access denied'])),
        ]);

        $this->expectException(ForbiddenException::class);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);
    }

    public function testInsufficientCreditsException(): void
    {
        $client = $this->makeClient([
            new Response(403, [], json_encode([
                'message' => 'Insufficient credits',
                'code' => 'insufficient_credits',
                'required_credits' => 10.0,
                'remaining_credits' => 5.0,
                'shortfall_credits' => 5.0,
                'reset_at' => '2026-02-01',
                'upgrade_url' => 'https://socialkit.dev/upgrade',
                'top_up_url' => 'https://socialkit.dev/topup',
                'incident_id' => 'inc-123',
            ])),
        ]);

        try {
            $client->request('/youtube/transcript', ['url' => 'https://test.com']);
            self::fail('Expected InsufficientCreditsException');
        } catch (InsufficientCreditsException $e) {
            self::assertSame(10.0, $e->requiredCredits);
            self::assertSame(5.0, $e->remainingCredits);
            self::assertSame(5.0, $e->shortfallCredits);
            self::assertSame('2026-02-01', $e->resetAt);
            self::assertSame('https://socialkit.dev/upgrade', $e->upgradeUrl);
            self::assertSame('https://socialkit.dev/topup', $e->topUpUrl);
            self::assertSame('inc-123', $e->incidentId);
        }
    }

    public function testNotFoundException(): void
    {
        $client = $this->makeClient([
            new Response(404, [], json_encode(['message' => 'Video not found'])),
        ]);

        $this->expectException(NotFoundException::class);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);
    }

    public function testRateLimitException(): void
    {
        $client = $this->makeClient([
            new Response(429, ['Retry-After' => '60'], json_encode(['message' => 'Rate limited'])),
        ]);

        try {
            $client->request('/youtube/transcript', ['url' => 'https://test.com']);
            self::fail('Expected RateLimitException');
        } catch (RateLimitException $e) {
            self::assertSame(60, $e->retryAfter);
            self::assertSame(429, $e->httpStatus);
        }
    }

    public function testServerException500(): void
    {
        $client = $this->makeClient([
            new Response(500, [], json_encode(['message' => 'Internal server error'])),
        ]);

        $this->expectException(ServerException::class);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);
    }

    public function testServerException503(): void
    {
        $client = $this->makeClient([
            new Response(503, [], json_encode(['message' => 'Service unavailable'])),
        ]);

        $this->expectException(ServerException::class);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);
    }

    public function testDecodeException(): void
    {
        $client = $this->makeClient([
            new Response(200, ['Content-Type' => 'application/json'], 'invalid json {{{'),
        ]);

        $this->expectException(DecodeException::class);

        $client->request('/youtube/transcript', ['url' => 'https://test.com']);
    }

    public function testAllExceptionsExtendSocialKitException(): void
    {
        self::assertInstanceOf(SocialKitException::class, new BadRequestException('test', 400));
        self::assertInstanceOf(SocialKitException::class, new AuthenticationException('test', 401));
        self::assertInstanceOf(SocialKitException::class, new ForbiddenException('test', 403));
        self::assertInstanceOf(SocialKitException::class, new NotFoundException('test', 404));
        self::assertInstanceOf(SocialKitException::class, new RateLimitException('test', 429));
        self::assertInstanceOf(SocialKitException::class, new ServerException('test', 500));
        self::assertInstanceOf(SocialKitException::class, new TransportException('test'));
        self::assertInstanceOf(SocialKitException::class, new TimeoutException('test'));
        self::assertInstanceOf(SocialKitException::class, new DecodeException('test'));
        self::assertInstanceOf(SocialKitException::class, new AsyncJobFailedException('test'));
    }

    public function testInsufficientCreditsExtendsForbidden(): void
    {
        $e = InsufficientCreditsException::fromResponse(403, [
            'message' => 'test',
            'required_credits' => 5.0,
        ]);

        self::assertInstanceOf(ForbiddenException::class, $e);
        self::assertInstanceOf(SocialKitException::class, $e);
    }

    public function testExceptionCarriesMeta(): void
    {
        $client = $this->makeClient([
            new Response(429, [
                'Retry-After' => '30',
                'X-Credits-Remaining' => '0',
            ], json_encode(['message' => 'Rate limited'])),
        ]);

        try {
            $client->request('/youtube/transcript', ['url' => 'https://test.com']);
            self::fail('Expected RateLimitException');
        } catch (RateLimitException $e) {
            self::assertNotNull($e->getMeta());
            self::assertSame(0.0, $e->getMeta()->creditsRemaining);
            self::assertSame(30, $e->getMeta()->retryAfter);
        }
    }
}
