<?php

namespace App\Services;

use App\Enums\FacebookMetricsNamesEnum;
use App\Helpers\DataboxHelper;
use App\Models\DataboxMetricsRequestLog;
use App\Models\ProviderMetricsRequestLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;

abstract class MetricsService
{
    // HTTP client for making requests
    protected Request $request;

    // Constructor for injecting HTTP client
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function sendDataToDatabox(array $metricsArray): void
    {
        $token = getenv("DATABOX_PUSH_TOKEN");

        $databoxHelper = new DataboxHelper($token);
        $nowDate = Carbon::now();

        foreach ($metricsArray as $metricKey => $metricValue) {
            $databoxMetricKey = FacebookMetricsNamesEnum::getDataboxMetricFromFacebookMetric($metricKey);
            try {
                $result = $databoxHelper->push($databoxMetricKey, $metricValue, $nowDate);
            } catch (Exception $e) {
                $this->logDataboxRequestError($databoxMetricKey, $e->getMessage());

                continue;
            }

            $this->logDataboxRequestSuccess($databoxMetricKey, $result['message'], $result['id']);
        }
    }

    protected function logDataboxRequestError(string $metricKey, string $errorMessage): void
    {
        DataboxMetricsRequestLog::create([
            'metric_name' => $metricKey,
            'response_message' => $errorMessage,
            'is_success' => false,
        ]);
    }

    protected function logDataboxRequestSuccess(string $metricKey, string $responseMessage, string $responseId): void
    {
        DataboxMetricsRequestLog::create([
            'metric_name' => $metricKey,
            'response_message' => $responseMessage,
            'response_id' => $responseId,
            'is_success' => true,
        ]);
    }

    // Log response details
    protected function handleProviderResponse(Response $response, string $apiProvider, string $endpoint): void
    {
        if ($response->successful()) {
            // Log success response
            $this->logProviderRequestSuccess($response, $apiProvider, $endpoint);
        } else {
            // Log error response
            $this->logProviderRequestError($response->object()->error, $apiProvider, $endpoint);
        }
    }


    protected function logProviderRequestSuccess(Response $response, string $apiProvider, string $endpoint): void
    {
        ProviderMetricsRequestLog::create([
            'api_provider' => $apiProvider,
            'endpoint' => $response->effectiveUri()->getPath(),
            'response_code' => $response->getStatusCode(),
            'is_success' => true,
        ]);
    }

    protected function logProviderRequestError(Exception $e, string $apiProvider, string $endpoint): void
    {
        ProviderMetricsRequestLog::create([
            'api_provider' => $apiProvider,
            'endpoint' => $endpoint,
            'response_message' => $e->getMessage(),
            'response_code' => $e->getCode(),
            'is_success' => false,
        ]);
    }
}