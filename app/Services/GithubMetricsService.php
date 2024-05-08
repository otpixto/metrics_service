<?php

namespace App\Services;

use App\Enums\FacebookMetricsNamesEnum;
use App\Enums\MetricsProvidersEndpointsEnum;
use App\Enums\MetricsProvidersNamesEnum;
use App\Models\FacebookMetric;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GithubMetricsService extends MetricsService
{
    // Define Facebook API version
    private const string GITHUB_REPOSITORY_OWNER = 'otpixto';
    private const string GITHUB_REPOSITORY_NAME = 'psychology_api';

    // Handle request, retrieve Facebook metrics and send it to Databox
    public function handle(): array
    {
        // Retrieve Facebook access token from request
        $githubAccessToken = $this->request->get('github_access_token');

        // Array to store retrieved metrics
        $metricsArray = [];

        // Get friends count from Facebook
        $commitsCountResponse = $this->getResponseFromGithub('commits', $githubAccessToken);

        dd($commitsCountResponse);

        if ($commitsCountResponse && $commitsCountResponse->successful()) {
            $totalCount = $commitsCountResponse->json()['summary']['total_count'] ?? null;

            if ($totalCount) {
                $metricsArray[FacebookMetricsNamesEnum::TOTAL_FRIENDS_AMOUNT->value] = $totalCount;
            }
        }


        // Store metrics in database and send to Databox if any metrics retrieved
        if ($metricsArray) {
            FacebookMetric::create($metricsArray);

            parent::sendDataToDatabox($metricsArray);
        }

        return $metricsArray;
    }

    // Make request to Facebook API
    private function getResponseFromGithub(string $metric, string $githubAccessToken, string $fieldsString = ''): PromiseInterface|Response|null
    {
        try {
            $response = Http::withUrlParameters([
                'endpoint' => MetricsProvidersEndpointsEnum::GITHUB_ENDPOINT->value,
                'owner' => self::GITHUB_REPOSITORY_OWNER,
                'page' => self::GITHUB_REPOSITORY_NAME,
                'repo' => $metric,
            ])->get('{+endpoint}/repos/{owner}/{repo}/{metric}', [
                'fields' => $fieldsString,
                'access_token' => $githubAccessToken,
            ]);
        } catch (Exception $e) {
            // Log error if request fails
            $this->logProviderRequestError($e, MetricsProvidersNamesEnum::FACEBOOK->value, MetricsProvidersEndpointsEnum::FACEBOOK_ENDPOINT->value);

            return null;
        }

        // Log response details
        $this->handleProviderResponse($response, MetricsProvidersNamesEnum::FACEBOOK->value, MetricsProvidersEndpointsEnum::FACEBOOK_ENDPOINT->value);

        return $response;
    }
}
