<?php

declare(strict_types=1);

use App\Http\Controllers\ClubController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('clubs', ClubController::class);
});
