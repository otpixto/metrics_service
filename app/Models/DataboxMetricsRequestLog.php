<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(string[] $array)
 */
class DataboxMetricsRequestLog extends Model
{
    protected $fillable = [
        'metric_name',
        'metrics_amount',
        'response_message',
        'response_id',
        'is_success',
    ];
}
