<?php

declare(strict_types=1);

use App\Http\Controllers\ClubRewards\RewardController;
use App\Http\Controllers\ClubRewards\RewardLevelController;
use App\Http\Controllers\Clubs\ClubLocationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::resource('locations', ClubLocationController::class);

    // Reward Levels
    Route::resource('reward-levels', RewardLevelController::class);

    // Rewards
    Route::resource('reward-levels.rewards', RewardController::class)->except(['index', 'show']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/clubs.php';
