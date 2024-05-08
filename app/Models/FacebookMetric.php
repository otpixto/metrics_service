<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(string[] $array)
 */
class FacebookMetric extends Model
{
    protected $fillable = [
        'total_friends_amount',
        'photo_width',
        'photo_height',
    ];
}
