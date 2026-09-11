<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\BulkRequest;

final class BulkTest extends TestCase
{
    public function testYouTubeBulkTranscript(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    ['url' => 'https://youtube.com/watch?v=1', 'transcript' => 'T1'],
                    ['url' => 'https://youtube.com/watch?v=2', 'transcript' => 'T2'],
                ],
            ]),
        ]);

        $response = $client->youtube()->bulkTranscript(new BulkRequest(
            requests: [
                ['url' => 'https://youtube.com/watch?v=1'],
                ['url' => 'https://youtube.com/watch?v=2'],
            ],
        ));

        self::assertTrue($response->success);

        $request = $this->lastRequest();
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/youtube/transcript/bulk', $request->getUri()->getPath());

        $body = json_decode((string) $request->getBody(), true);
        self::assertCount(2, $body['requests']);
    }

    public function testYouTubeBulkComments(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => []]),
        ]);

        $client->youtube()->bulkComments(new BulkRequest(
            requests: [['url' => 'https://youtube.com/watch?v=1']],
        ));

        $request = $this->lastRequest();
        self::assertSame('/youtube/comments/bulk', $request->getUri()->getPath());
    }

    public function testYouTubeBulkStats(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => []]),
        ]);

        $client->youtube()->bulkStats(new BulkRequest(
            requests: [['url' => 'https://youtube.com/watch?v=1']],
        ));

        $request = $this->lastRequest();
        self::assertSame('/youtube/stats/bulk', $request->getUri()->getPath());
    }

    public function testYouTubeBulkSummarize(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => []]),
        ]);

        $client->youtube()->bulkSummarize(new BulkRequest(
            requests: [['url' => 'https://youtube.com/watch?v=1']],
        ));

        $request = $this->lastRequest();
        self::assertSame('/youtube/summarize/bulk', $request->getUri()->getPath());
    }

    public function testTikTokBulkTranscript(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => []]),
        ]);

        $client->tiktok()->bulkTranscript(new BulkRequest(
            requests: [['url' => 'https://tiktok.com/@user/video/1']],
        ));

        $request = $this->lastRequest();
        self::assertSame('/tiktok/transcript/bulk', $request->getUri()->getPath());
    }

    public function testInstagramBulkStats(): void
    {
        $client = $this->makeClient([
            $this->json(['success' => true, 'data' => []]),
        ]);

        $client->instagram()->bulkStats(new BulkRequest(
            requests: [['url' => 'https://instagram.com/p/abc']],
        ));

        $request = $this->lastRequest();
        self::assertSame('/instagram/stats/bulk', $request->getUri()->getPath());
    }

    public function testBulkRequestToArray(): void
    {
        $req = new BulkRequest(
            requests: [['url' => 'https://test.com'], ['url' => 'https://test2.com']],
        );

        $arr = $req->toArray();
        self::assertCount(2, $arr['requests']);
    }
}
