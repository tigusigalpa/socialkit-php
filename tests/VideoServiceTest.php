<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\Summary;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Dto\VideoSummaryRequest;
use Tigusigalpa\SocialKit\Dto\VideoTranscriptRequest;

final class VideoServiceTest extends TestCase
{
    public function testTranscript(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://example.com/video.mp4',
                    'transcript' => 'Direct video transcript',
                    'duration_seconds' => 120.0,
                    'credits_used' => 10.0,
                ],
            ]),
        ]);

        $response = $client->video()->transcript(new VideoTranscriptRequest(
            url: 'https://example.com/video.mp4',
        ));

        self::assertInstanceOf(Transcript::class, $response->data);
        self::assertSame('Direct video transcript', $response->data->transcript);
        self::assertSame(10.0, $response->data->creditsUsed);

        $body = json_decode((string) $this->lastRequest()->getBody(), true);
        self::assertSame('https://example.com/video.mp4', $body['url']);
    }

    public function testSummarize(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://example.com/video.mp4',
                    'summary' => 'Direct video summary',
                    'credits_used' => 10.0,
                ],
            ]),
        ]);

        $response = $client->video()->summarize(new VideoSummaryRequest(
            url: 'https://example.com/video.mp4',
        ));

        self::assertInstanceOf(Summary::class, $response->data);
        self::assertSame('Direct video summary', $response->data->summary);
    }
}
