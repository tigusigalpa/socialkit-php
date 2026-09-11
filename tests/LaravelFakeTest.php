<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Illuminate\Support\Facades\Facade;
use Tigusigalpa\SocialKit\Dto\Status;
use Tigusigalpa\SocialKit\Laravel\Facades\SocialKit;
use Tigusigalpa\SocialKit\Laravel\SocialKitFake;

final class LaravelFakeTest extends TestCase
{
    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();

        parent::tearDown();
    }

    public function testFakeServicesDispatchThroughTheFake(): void
    {
        $fake = new SocialKitFake();
        $fake->queueData(['overall' => 'green']);

        $response = $fake->status()->status();

        self::assertInstanceOf(Status::class, $response->data);
        self::assertSame('green', $response->data->overall);
        $fake->assertRequested('/status');
        $fake->assertRequestCount(1);
    }

    public function testFacadeFakeReplacesTheResolvedClient(): void
    {
        $fake = SocialKit::fake();
        $fake->queueData(['overall' => 'green']);

        $response = SocialKit::status()->status();

        self::assertInstanceOf(Status::class, $response->data);
        $fake->assertRequested('/status');
    }
}
