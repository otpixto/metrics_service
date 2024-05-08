<?php

namespace App\Http\Controllers;


use App\Models\DataboxMetricsRequestLog;
use App\Models\FacebookMetric;
use App\Models\GithubMetric;
use App\Models\ProviderMetricsRequestLog;
use App\Services\FacebookMetricsService;
use App\Services\GithubMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function handleGithubMetrics(Request $request): JsonResponse
    {
        $request->validate([
            'github_access_token' => 'required|string',
        ]);

        $data = (new GithubMetricsService($request))->handle();

        return response()->json(['data' => $data]);
    }


    public function handleFacebookMetrics(Request $request): JsonResponse
    {
        $request->validate([
            'facebook_access_token' => 'required|string',
        ]);

        $data = (new FacebookMetricsService($request))->handle();

        return response()->json(['data' => $data]);
    }

    public function metricsList(): JsonResponse
    {
        return response()->json(['data' => [
            'facebook_metric' => FacebookMetric::all(),
            'github_metric' => GithubMetric::all(),
            'provider_metrics_request_log' => ProviderMetricsRequestLog::all(),
            'databox_metrics_request_log' => DataboxMetricsRequestLog::all(),
        ]]);
    }
}
