<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/uploads/video/put-url', [\App\Http\Controllers\UploadController::class, 'videoPutUrl']);
});