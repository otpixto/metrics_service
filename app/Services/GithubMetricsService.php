<?php

namespace App\Services;

use App\Enums\GithubMetricsNamesEnum;
use App\Enums\MetricsProvidersEndpointsEnum;
use App\Enums\MetricsProvidersNamesEnum;
use App\Models\GithubMetric;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GithubMetricsService extends MetricsService
{
    // GitHub repository owner name
    private const string GITHUB_REPOSITORY_OWNER = 'otpixto';
    // GitHub repository name
    private const string GITHUB_REPOSITORY_NAME = 'psychology_api';

    // Handle request, retrieve GitHub metrics and send it to Databox
    public function handle(): array
    {
        // Retrieve GitHub access token from request
        $githubAccessToken = $this->request->get('github_access_token');

        // Array to store retrieved metrics
        $metricsArray = [];

        // Get commits amount from GitHub
        $commitsCountResponse = $this->getResponseFromGithub($githubAccessToken);

        // Check if response is successful and parse the number of commits
        if ($commitsCountResponse && $commitsCountResponse->successful()) {
            $totalAmount = count($commitsCountResponse->json());

            // If commits count is non-zero, add it to metrics array
            if ($totalAmount) {
                $metricsArray[GithubMetricsNamesEnum::TOTAL_COMMITS_AMOUNT->value] = $totalAmount;
            }
        }

        // Store metrics in database and send to Databox if any metrics retrieved
        if ($metricsArray) {
            GithubMetric::create($metricsArray);
            parent::sendDataToDatabox($metricsArray, GithubMetricsNamesEnum::class);
        }

        return $metricsArray;
    }

    // Make request to GitHub API to get commits count
    private function getResponseFromGithub(string $githubAccessToken): PromiseInterface|Response|null
    {
        try {
            $response = Http::withUrlParameters([
                'endpoint' => MetricsProvidersEndpointsEnum::GITHUB_ENDPOINT->value,
                'owner' => self::GITHUB_REPOSITORY_OWNER,
                'repo' => self::GITHUB_REPOSITORY_NAME,
                'metric' => 'commits',
            ])
                ->withHeaders([
                    'Accept' => 'application/vnd.github.v3+json',
                    'Authorization' => 'Bearer ' . $githubAccessToken,
                ])
                ->get('{+endpoint}/repos/{owner}/{repo}/{metric}');

        } catch (Exception $e) {
            // Log error if request fails
            $this->logProviderRequestError($e, MetricsProvidersNamesEnum::GITHUB->value, MetricsProvidersEndpointsEnum::GITHUB_ENDPOINT->value);

            return null;
        }

        // Log response details
        $this->handleProviderResponse($response, MetricsProvidersNamesEnum::GITHUB->value, MetricsProvidersEndpointsEnum::GITHUB_ENDPOINT->value);

        return $response;
    }
}
