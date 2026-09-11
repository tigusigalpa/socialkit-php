<?php

declare(strict_types=1);

namespace Tigusigalpa\SocialKit\Services;

use Tigusigalpa\SocialKit\ApiResponse;
use Tigusigalpa\SocialKit\Dto\CompanyPostsRequest;
use Tigusigalpa\SocialKit\Dto\CompanyRequest;
use Tigusigalpa\SocialKit\Dto\LinkedInCompany;
use Tigusigalpa\SocialKit\Dto\LinkedInCompanyPosts;
use Tigusigalpa\SocialKit\Dto\LinkedInPost;
use Tigusigalpa\SocialKit\Dto\LinkedInProfile;
use Tigusigalpa\SocialKit\Dto\PostRequest;
use Tigusigalpa\SocialKit\Dto\ProfileRequest;
use Tigusigalpa\SocialKit\Dto\Transcript;
use Tigusigalpa\SocialKit\Dto\TranscriptRequest;
use Tigusigalpa\SocialKit\Exceptions\BadRequestException;

/**
 * Service for the SocialKit LinkedIn endpoints.
 *
 * @see https://docs.socialkit.dev/linkedin
 */
final class LinkedInService extends AbstractService
{
    /**
     * Fetch a LinkedIn profile (with recentArticles[]).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/linkedin-profile-api
     *
     * @param ProfileRequest $req
     *
     * @return ApiResponse<LinkedInProfile>
     */
    public function profile(ProfileRequest $req): ApiResponse
    {
        return $this->postRequest('/linkedin/profile', $req->toArray(), LinkedInProfile::class);
    }

    /**
     * Fetch a LinkedIn company.
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/linkedin-company-api
     *
     * @param CompanyRequest $req
     *
     * @return ApiResponse<LinkedInCompany>
     */
    public function company(CompanyRequest $req): ApiResponse
    {
        return $this->postRequest('/linkedin/company', $req->toArray(), LinkedInCompany::class);
    }

    /**
     * Fetch a company's posts (max 50).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/linkedin-company-api-posts
     *
     * @param CompanyPostsRequest $req
     *
     * @return ApiResponse<LinkedInCompanyPosts>
     *
     * @throws BadRequestException If limit > 50.
     */
    public function companyPosts(CompanyPostsRequest $req): ApiResponse
    {
        if ($req->limit !== null && $req->limit > 50) {
            throw new BadRequestException('LinkedIn company posts limit must not exceed 50.');
        }

        return $this->postRequest('/linkedin/company-posts', $req->toArray(), LinkedInCompanyPosts::class);
    }

    /**
     * Fetch a single LinkedIn post (primary imageUrl, images[], nullable videoUrl, nested author).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/linkedin-post-api
     *
     * @param PostRequest $req
     *
     * @return ApiResponse<LinkedInPost>
     */
    public function post(PostRequest $req): ApiResponse
    {
        return $this->postRequest('/linkedin/post', $req->toArray(), LinkedInPost::class);
    }

    /**
     * Fetch a transcript (segments have text/start/duration but NO timestamp).
     *
     * Upstream docs: https://docs.socialkit.dev/api-reference/linkedin-transcript-api
     *
     * @param TranscriptRequest $req
     *
     * @return ApiResponse<Transcript>
     */
    public function transcript(TranscriptRequest $req): ApiResponse
    {
        return $this->postRequest('/linkedin/transcript', $req->toArray(), Transcript::class);
    }
}
