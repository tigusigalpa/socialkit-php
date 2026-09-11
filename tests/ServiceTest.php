<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\Credits;
use Tigusigalpa\SocialKit\Dto\Status;

final class ServiceTest extends TestCase
{
    public function testStatusReturnsTypedDto(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'overall' => 'green',
                    'generated_at' => '2026-01-01T00:00:00Z',
                    'tools' => [
                        [
                            'id' => 'youtube',
                            'channel' => 'youtube',
                            'name' => 'YouTube',
                            'status' => 'operational',
                            'message' => 'All systems operational',
                            'updated_at' => '2026-01-01T00:00:00Z',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $client->status()->status();

        self::assertInstanceOf(Status::class, $response->data);
        self::assertSame('green', $response->data->overall);
        self::assertSame('2026-01-01T00:00:00Z', $response->data->generatedAt);
        self::assertCount(1, $response->data->tools);
        self::assertSame('youtube', $response->data->tools[0]->id);
        self::assertSame('YouTube', $response->data->tools[0]->name);
        self::assertSame('operational', $response->data->tools[0]->status);

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/status', $request->getUri()->getPath());
    }

    public function testCreditsReturnsTypedDto(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'total_remaining' => 500.0,
                    'monthly' => [
                        'remaining' => 300.0,
                        'used' => 200.0,
                        'total' => 500.0,
                    ],
                    'purchased' => [
                        'remaining' => 200.0,
                        'used' => 0.0,
                        'total' => 200.0,
                    ],
                ],
            ]),
        ]);

        $response = $client->status()->credits();

        self::assertInstanceOf(Credits::class, $response->data);
        self::assertSame(500.0, $response->data->totalRemaining);
        self::assertNotNull($response->data->monthly);
        self::assertSame(300.0, $response->data->monthly->remaining);
        self::assertSame(200.0, $response->data->monthly->used);
        self::assertNotNull($response->data->purchased);
        self::assertSame(200.0, $response->data->purchased->remaining);

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/credits', $request->getUri()->getPath());
        self::assertSame('test-key-12345', $request->getHeaderLine('x-access-key'));
    }
}
