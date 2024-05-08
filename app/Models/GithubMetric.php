<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(int[] $array)
 */
class GithubMetric extends Model
{
    protected $fillable = [
        'total_commits_amount'
    ];
}
