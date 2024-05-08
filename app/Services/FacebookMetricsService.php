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

class FacebookMetricsService extends MetricsService
{
    // Define Facebook API version
    private const string FACEBOOK_API_VERSION = 'v18.0';

    // Handle request, retrieve Facebook metrics and send it to Databox
    public function handle(): array
    {
        // Retrieve Facebook access token from request
        $facebookAccessToken = $this->request->get('facebook_access_token');

        // Array to store retrieved metrics
        $metricsArray = [];

        // Get friends count from Facebook
        $friendsCountResponse = $this->getResponseFromFacebook('friends', $facebookAccessToken);

        if ($friendsCountResponse && $friendsCountResponse->successful()) {
            $totalCount = $friendsCountResponse->json()['summary']['total_count'] ?? null;

            if ($totalCount) {
                $metricsArray[FacebookMetricsNamesEnum::TOTAL_FRIENDS_AMOUNT->value] = $totalCount;
            }
        }

        // Get photo data from Facebook
        $photoDataResponse = $this->getResponseFromFacebook('picture', $facebookAccessToken, 'width,height');

        if ($photoDataResponse && $photoDataResponse->successful()) {
            $photoDataArray = $photoDataResponse->json()['data'] ?? null;

            if ($photoDataArray) {
                $metricsArray += [
                    FacebookMetricsNamesEnum::PHOTO_WIDTH->value => $photoDataArray['width'] ?? null,
                    FacebookMetricsNamesEnum::PHOTO_HEIGHT->value => $photoDataArray['height'] ?? null,
                ];
            }
        }

        // Store metrics in database and send to Databox if any metrics retrieved
        if ($metricsArray) {
            FacebookMetric::create($metricsArray);

            parent::sendDataToDatabox($metricsArray, FacebookMetricsNamesEnum::class);
        }

        return $metricsArray;
    }

    // Make request to Facebook API
    private function getResponseFromFacebook(string $metric, string $facebookAccessToken, string $fieldsString = ''): PromiseInterface|Response|null
    {
        try {
            $response = Http::withUrlParameters([
                'endpoint' => MetricsProvidersEndpointsEnum::FACEBOOK_ENDPOINT->value,
                'apiVersion' => self::FACEBOOK_API_VERSION,
                'page' => 'me',
                'metric' => $metric,
            ])->get('{+endpoint}/{apiVersion}/{page}/{metric}', [
                'fields' => $fieldsString,
                'access_token' => $facebookAccessToken,
                'redirect' => 0,
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
