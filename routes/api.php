<?php

use App\Http\Controllers\LeaderboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'store']);
    Route::post('/{user}/score', [UserController::class, 'addScore']);
});

Route::prefix('leaderboard')->group(function () {
    Route::get('/top', [LeaderboardController::class, 'top']);
    Route::get('/rank/{user}', [LeaderboardController::class, 'rank']);
});
