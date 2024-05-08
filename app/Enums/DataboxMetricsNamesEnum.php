<?php

namespace App\Enums;

enum DataboxMetricsNamesEnum: string
{
    case FACEBOOK_TOTAL_FRIENDS_AMOUNT = 'facebook_total_friends_amount';
    case FACEBOOK_PHOTO_WIDTH = 'facebook_photo_width';
    case FACEBOOK_PHOTO_HEIGHT = 'facebook_photo_height';
    case GITHUB_COMMITS_AMOUNT = 'github_commits_amount';
}