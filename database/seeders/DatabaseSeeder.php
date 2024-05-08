<?php

namespace Database\Seeders;

use App\Enums\MetricsProvidersEndpointsEnum;
use App\Enums\MetricsProvidersNamesEnum;
use App\Models\Account;
use App\Models\Contact;
use App\Models\FacebookMetric;
use App\Models\GithubMetric;
use App\Models\Organization;
use App\Models\ProviderMetricsRequestLog;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        FacebookMetric::create([
            'total_friends_amount' => 86,
            'photo_width' => 150,
            'photo_height' => 150,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        GithubMetric::create([
            'total_friends_amount' => 86,
            'photo_width' => 150,
            'photo_height' => 150,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        ProviderMetricsRequestLog::insert([
            [
                'api_provider' => MetricsProvidersNamesEnum::FACEBOOK->value,
                'endpoint' => MetricsProvidersEndpointsEnum::FACEBOOK_ENDPOINT->value,
                'response_message' => 'Error validating access token: Session has expired on Monday, 06-May-24 12:00:00 PDT. The current time is Tuesday, 07-May-24 01:43:38 PDT.',
                'response_type' => 'OAuthException',
                'response_trace_id' => 'AWb4nBQq51QT5F5WHG59-t3',
                'response_code' => 190,
                'is_success' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'api_provider' => MetricsProvidersNamesEnum::GITHUB->value,
                'endpoint' => MetricsProvidersEndpointsEnum::GITHUB_ENDPOINT->value,
                'response_message' => 'Github access denied',
                'response_type' => 'OAuthException',
                'response_trace_id' => 'erte432f23',
                'response_code' => 190,
                'is_success' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'api_provider' => MetricsProvidersNamesEnum::FACEBOOK->value,
                'endpoint' => MetricsProvidersEndpointsEnum::FACEBOOK_ENDPOINT->value,
                'response_message' => 'Success',
                'response_type' => null,
                'response_trace_id' => null,
                'response_code' => 200,
                'is_success' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'api_provider' => MetricsProvidersNamesEnum::GITHUB->value,
                'endpoint' => MetricsProvidersEndpointsEnum::GITHUB_ENDPOINT->value,
                'response_message' => 'Success',
                'response_type' => null,
                'response_trace_id' => null,
                'response_code' => 200,
                'is_success' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
