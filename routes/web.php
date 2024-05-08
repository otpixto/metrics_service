<?php

use App\Http\Controllers\MetricsController;
use Illuminate\Support\Facades\Route;

Route::get('/metrics/facebook', [MetricsController::class, 'handleFacebookMetrics']);
Route::get('/metrics/github', [MetricsController::class, 'handleGithubMetrics']);
Route::get('/metrics', [MetricsController::class, 'metricsList']);


