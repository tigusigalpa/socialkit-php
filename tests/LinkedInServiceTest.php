<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Tests;

use Tigusigalpa\SocialKit\Dto\CompanyPostsRequest;
use Tigusigalpa\SocialKit\Dto\CompanyRequest;
use Tigusigalpa\SocialKit\Dto\PostRequest;
use Tigusigalpa\SocialKit\Dto\ProfileRequest;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Dto\LinkedInCompany;
use Tigusigalpa\SocialKit\Dto\LinkedInCompanyPosts;
use Tigusigalpa\SocialKit\Dto\LinkedInPost;
use Tigusigalpa\SocialKit\Dto\LinkedInProfile;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

final class LinkedInServiceTest extends TestCase
{
    public function testProfileWithRecentArticles(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://linkedin.com/in/test',
                    'name' => 'Test User',
                    'headline' => 'Software Engineer',
                    'followers' => 5000,
                    'connections' => 1000,
                    'profile_image' => 'https://example.com/img.jpg',
                    'recent_articles' => [
                        ['title' => 'Article 1', 'url' => 'https://example.com/1', 'date' => '2026-01-01'],
                    ],
                ],
            ]),
        ]);

        $response = $client->linkedin()->profile(new ProfileRequest(
            url: 'https://linkedin.com/in/test',
        ));

        self::assertInstanceOf(LinkedInProfile::class, $response->data);
        self::assertSame('Test User', $response->data->name);
        self::assertSame(5000, $response->data->followers);
        self::assertCount(1, $response->data->recentArticles);
        self::assertSame('Article 1', $response->data->recentArticles[0]->title);
    }

    public function testCompany(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://linkedin.com/company/test',
                    'name' => 'Test Company',
                    'description' => 'A test company',
                    'followers' => 50000,
                    'employees' => 500,
                    'logo' => 'https://example.com/logo.jpg',
                ],
            ]),
        ]);

        $response = $client->linkedin()->company(new CompanyRequest(
            url: 'https://linkedin.com/company/test',
        ));

        self::assertInstanceOf(LinkedInCompany::class, $response->data);
        self::assertSame('Test Company', $response->data->name);
        self::assertSame(50000, $response->data->followers);
    }

    public function testCompanyPosts(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'posts' => [
                        [
                            'id' => '1',
                            'text' => 'Post 1',
                            'likes' => 100,
                            'comments' => 10,
                            'shares' => 5,
                            'image_url' => 'https://example.com/img.jpg',
                            'published_at' => '2026-01-01',
                            'author' => ['name' => 'Test Company'],
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $client->linkedin()->companyPosts(new CompanyPostsRequest(
            url: 'https://linkedin.com/company/test',
            limit: 20,
        ));

        self::assertInstanceOf(LinkedInCompanyPosts::class, $response->data);
        self::assertCount(1, $response->data->posts);
        self::assertSame('Post 1', $response->data->posts[0]->text);
        self::assertSame('https://example.com/img.jpg', $response->data->posts[0]->imageUrl);
        self::assertNotNull($response->data->posts[0]->author);
        self::assertSame('Test Company', $response->data->posts[0]->author['name']);
    }

    public function testCompanyPostsLimitExceeds50(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->makeClient([]);
        $client->linkedin()->companyPosts(new CompanyPostsRequest(
            url: 'https://linkedin.com/company/test',
            limit: 51,
        ));
    }

    public function testPost(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'id' => '123',
                    'text' => 'LinkedIn post text',
                    'likes' => 50,
                    'comments' => 5,
                    'image_url' => 'https://example.com/img.jpg',
                    'video_url' => null,
                    'published_at' => '2026-01-01',
                    'author' => ['name' => 'Author'],
                ],
            ]),
        ]);

        $response = $client->linkedin()->post(new PostRequest(
            url: 'https://linkedin.com/posts/test',
        ));

        self::assertInstanceOf(LinkedInPost::class, $response->data);
        self::assertSame('LinkedIn post text', $response->data->text);
        self::assertSame('https://example.com/img.jpg', $response->data->imageUrl);
        self::assertNull($response->data->videoUrl);
    }

    public function testTranscript(): void
    {
        $client = $this->makeClient([
            $this->json([
                'success' => true,
                'data' => [
                    'url' => 'https://linkedin.com/posts/test',
                    'transcript' => 'LinkedIn transcript',
                    'transcript_segments' => [
                        ['text' => 'Segment 1', 'start' => 0.0, 'duration' => 5.0],
                    ],
                ],
            ]),
        ]);

        $response = $client->linkedin()->transcript(new TranscriptRequest(
            url: 'https://linkedin.com/posts/test',
        ));

        self::assertInstanceOf(Transcript::class, $response->data);
        self::assertSame('LinkedIn transcript', $response->data->transcript);
        self::assertCount(1, $response->data->transcriptSegments);
        // LinkedIn segments have NO timestamp
        self::assertNull($response->data->transcriptSegments[0]->timestamp);
    }
}
