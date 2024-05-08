<?php

namespace App\Enums;

enum GithubMetricsNamesEnum: string
{
    case TOTAL_COMMITS_AMOUNT = 'total_commits_amount';

    public static function getDataboxMetricFromProviderMetric(string $githubMetricName): string
    {
        $githubToDataboxMetricMap = [
            self::TOTAL_COMMITS_AMOUNT->value => DataboxMetricsNamesEnum::GITHUB_COMMITS_AMOUNT->value,
        ];

        return $githubToDataboxMetricMap[$githubMetricName];
    }
}