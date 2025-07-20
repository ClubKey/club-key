<?php

declare(strict_types=1);

use App\Http\Controllers\Clubs\ClubController;
use App\Http\Controllers\Clubs\SwitchClubController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('clubs', ClubController::class);
    Route::post('clubs/{club}/switch', SwitchClubController::class)
        ->name('clubs.switch');
});
