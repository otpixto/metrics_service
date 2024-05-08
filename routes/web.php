<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\MetricsController;

Route::get('/metrics/facebook', [MetricsController::class, 'handleFacebookMetrics']);
//Route::get('/metrics/github', [MetricsController::class, 'handleGithubMetrics']);


