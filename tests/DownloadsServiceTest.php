<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use GuzzleHttp\Psr7\Response;
use Tigusigalpa\SocialKit\Dto\V2DownloadRequest;
use Tigusigalpa\SocialKit\Dto\V2Job;
use Tigusigalpa\SocialKit\Dto\WaitOptions;
use Tigusigalpa\SocialKit\Exceptions\AsyncJobFailedException;

final class DownloadsServiceTest extends TestCase
{
    public function testStart(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'job_id' => 'job-abc-123',
                    'status' => 'queued',
                    'platform' => 'youtube',
                    'url' => 'https://youtube.com/watch?v=test',
                    'quality' => '720p',
                    'format' => 'mp4',
                ],
            ]),
        ]);

        $response = $client->downloads()->start(new V2DownloadRequest(
            url: 'https://youtube.com/watch?v=test',
            platform: 'youtube',
        ));

        self::assertInstanceOf(V2Job::class, $response->data);
        self::assertSame('job-abc-123', $response->data->jobId);
        self::assertSame('queued', $response->data->status);
        self::assertSame('youtube', $response->data->platform);

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/v2/youtube/download', $request->getUri()->getPath());
    }

    public function testGetIsAlwaysGet(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'job_id' => 'job-abc-123',
                    'status' => 'processing',
                ],
            ]),
        ]);

        $response = $client->downloads()->get('job-abc-123');

        self::assertInstanceOf(V2Job::class, $response->data);
        self::assertSame('processing', $response->data->status);

        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/v2/downloads/job-abc-123', $request->getUri()->getPath());
    }

    public function testGetEscapesTheJobIdAsOnePathSegment(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['job_id' => 'job-1', 'status' => 'ready']]),
        ]);

        $client->downloads()->get('a/b?c#d e');

        self::assertSame('/v2/downloads/a%2Fb%3Fc%23d%20e', $this->lastRequest()->getUri()->getPath());
    }

    public function testGetRequiresAJobId(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->makeClient([])->downloads()->get('');
    }

    public function testWaitSucceedsWhenReady(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => ['job_id' => 'job-1', 'status' => 'ready', 'download_url' => 'https://cdn.test/dl.mp4'],
            ]),
        ]);

        $response = $client->downloads()->wait('job-1', new WaitOptions(
            interval: 0.01,
            timeout: 5.0,
        ));

        self::assertInstanceOf(V2Job::class, $response->data);
        self::assertSame('ready', $response->data->status);
        self::assertSame('https://cdn.test/dl.mp4', $response->data->downloadUrl);
        self::assertCount(1, $this->history);
    }

    public function testWaitPollsUntilReady(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['job_id' => 'job-1', 'status' => 'queued']]),
            $this->json(['success' => true, 'data' => ['job_id' => 'job-1', 'status' => 'processing']]),
            $this->json(['success' => true, 'data' => ['job_id' => 'job-1', 'status' => 'ready', 'download_url' => 'https://cdn.test/dl.mp4']]),
        ]);

        $response = $client->downloads()->wait('job-1', new WaitOptions(
            interval: 0.01,
            timeout: 10.0,
        ));

        self::assertSame('ready', $response->data->status);
        self::assertCount(3, $this->history);
    }

    public function testWaitThrowsOnFailed(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'job_id' => 'job-1',
                    'status' => 'failed',
                    'error' => 'Video too long',
                    'error_code' => 'MAX_DURATION_EXCEEDED',
                    'retryable' => false,
                ],
            ]),
        ]);

        $this->expectException(AsyncJobFailedException::class);

        $client->downloads()->wait('job-1', new WaitOptions(
            interval: 0.01,
            timeout: 5.0,
        ));
    }

    public function testWaitRespectsMaxPolls(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => ['job_id' => 'job-1', 'status' => 'processing']]),
            $this->json(['success' => true, 'data' => ['job_id' => 'job-1', 'status' => 'processing']]),
        ]);

        $response = $client->downloads()->wait('job-1', new WaitOptions(
            interval: 0.01,
            timeout: 10.0,
            maxPolls: 2,
        ));

        // Should return the last response (still processing)
        self::assertSame('processing', $response->data->status);
        self::assertCount(2, $this->history);
    }

    public function testV2JobIsTerminal(): void
    {
        $ready = V2Job::fromArray(['job_id' => '1', 'status' => 'ready']);
        self::assertTrue($ready->isTerminal());
        self::assertTrue($ready->isReady());
        self::assertFalse($ready->isFailed());

        $failed = V2Job::fromArray(['job_id' => '1', 'status' => 'failed']);
        self::assertTrue($failed->isTerminal());
        self::assertFalse($failed->isReady());
        self::assertTrue($failed->isFailed());

        $processing = V2Job::fromArray(['job_id' => '1', 'status' => 'processing']);
        self::assertFalse($processing->isTerminal());
    }

    public function testV2DownloadRequestValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new V2DownloadRequest(
            url: 'https://test.com',
            platform: 'invalid',
        );
    }

    public function testV2DownloadRequestNegativeMaxDuration(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new V2DownloadRequest(
            url: 'https://test.com',
            maxDuration: -1,
        );
    }

    public function testWaitOptionsRejectNonPositiveInterval(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new WaitOptions(interval: 0.0);
    }
}
