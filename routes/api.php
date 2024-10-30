<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{AuthController, CommentController};

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('comments', CommentController::class)->except(['show']);;
    });
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
