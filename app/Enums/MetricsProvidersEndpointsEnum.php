<?php

namespace App\Enums;

enum MetricsProvidersEndpointsEnum: string
{
    case FACEBOOK_ENDPOINT = 'https://graph.facebook.com';
    case GITHUB_ENDPOINT = 'https://api.github.com/';
}