<?php

use Illuminate\Support\Facades\Route;

// TODO - add versioning
Route::apiResource('submissions', \App\Http\Controllers\Api\SubmissionController::class)
    ->only(['store', 'show']);
