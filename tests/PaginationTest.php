<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\CommentsRequest;
use Tigusigalpa\SocialKit\Dto\Comments;

final class PaginationTest extends TestCase
{
    public function testCursorRoundtripInComments(): void
    {
        // First page
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'video_id' => 'test',
                    'comments' => [['id' => '1', 'text' => 'Comment 1']],
                    'has_more' => true,
                    'cursor' => 'cursor-page-2',
                ],
            ]),
        ]);

        $response1 = $client->youtube()->comments(new CommentsRequest(
            url: 'https://youtube.com/watch?v=test',
            limit: 50,
        ));

        self::assertInstanceOf(Comments::class, $response1->data);
        self::assertTrue($response1->data->hasMore);
        self::assertSame('cursor-page-2', $response1->data->cursor);

        // Second page with cursor
        $client2 = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'video_id' => 'test',
                    'comments' => [['id' => '2', 'text' => 'Comment 2']],
                    'has_more' => false,
                    'cursor' => null,
                ],
            ]),
        ]);

        $response2 = $client2->youtube()->comments(new CommentsRequest(
            url: 'https://youtube.com/watch?v=test',
            limit: 50,
            cursor: $response1->data->cursor,
        ));

        self::assertFalse($response2->data->hasMore);
        self::assertNull($response2->data->cursor);

        // Verify cursor was sent in the request body
        $body = json_decode((string) $this->lastRequest()->getBody(), true);
        self::assertSame('cursor-page-2', $body['cursor']);
    }

    public function testNullCursorInResponse(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'comments' => [],
                    'has_more' => false,
                    'cursor' => null,
                ],
            ]),
        ]);

        $response = $client->youtube()->comments(new CommentsRequest(
            url: 'https://youtube.com/watch?v=test',
        ));

        self::assertNull($response->data->cursor);
        self::assertFalse($response->data->hasMore);
    }

    public function testOpaqueCursorPassedVerbatim(): void
    {
        $opaqueCursor = 'eyJwYWdlIjoyLCJ0b2tlbiI6ImFiYzEyMyJ9==';

        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'comments' => [],
                    'has_more' => false,
                    'cursor' => null,
                ],
            ]),
        ]);

        $client->youtube()->comments(new CommentsRequest(
            url: 'https://youtube.com/watch?v=test',
            cursor: $opaqueCursor,
        ));

        $body = json_decode((string) $this->lastRequest()->getBody(), true);
        self::assertSame($opaqueCursor, $body['cursor']);
    }
}
