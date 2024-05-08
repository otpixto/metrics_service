<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(int[] $array)
 * @method static insert(array $array)
 */
class ProviderMetricsRequestLog extends Model
{
    protected $fillable = [
        'api_provider',
        'endpoint',
        'response_message',
        'response_type',
        'response_trace_id',
        'response_code',
        'is_success',
    ];
}
