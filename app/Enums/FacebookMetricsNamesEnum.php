<?php

namespace App\Enums;

enum FacebookMetricsNamesEnum: string
{
    case TOTAL_FRIENDS_AMOUNT = 'total_friends_amount';
    case PHOTO_WIDTH = 'photo_width';
    case PHOTO_HEIGHT = 'photo_height';

    public static function getDataboxMetricFromProviderMetric(string $facebookMetricName): string
    {
        $facebookToDataboxMetricMap = [
            self::TOTAL_FRIENDS_AMOUNT->value => DataboxMetricsNamesEnum::FACEBOOK_TOTAL_FRIENDS_AMOUNT->value,
            self::PHOTO_WIDTH->value => DataboxMetricsNamesEnum::FACEBOOK_PHOTO_WIDTH->value,
            self::PHOTO_HEIGHT->value => DataboxMetricsNamesEnum::FACEBOOK_PHOTO_HEIGHT->value,
        ];

        return $facebookToDataboxMetricMap[$facebookMetricName];
    }
}