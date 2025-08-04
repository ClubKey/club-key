<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminInvitationController;
use App\Http\Controllers\Admin\ClubAdminController;
use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\ClubLocationController;
use App\Http\Controllers\Admin\ClubUserController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\RewardLevelController;
use App\Http\Controllers\Admin\SwitchClubController;
use Illuminate\Support\Facades\Route;

Route::get('clubs/{club}/admin-invitations/{adminInvitation}/accept', [AdminInvitationController::class, 'accept'])
    ->name('clubs.admin-invitations.accept')
    ->middleware('signed');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('clubs', ClubController::class)->except(['show', 'index']);
    Route::get('clubs/{club}/danger-zone', [ClubController::class, 'dangerZone'])
        ->name('clubs.danger-zone');
    Route::post('clubs/{club}/switch', SwitchClubController::class)
        ->name('clubs.switch');
    Route::get('clubs/{club}/admins', [ClubAdminController::class, 'index'])
        ->name('clubs.admins.index');

    Route::resource('clubs.admin-invitations', AdminInvitationController::class)->only(['store', 'destroy']);
    Route::post('clubs/{club}/admin-invitations/{adminInvitation}/resend', [AdminInvitationController::class, 'resend'])
        ->name('clubs.admin-invitations.resend');

    Route::resource('locations', ClubLocationController::class);

    // Reward Levels
    Route::resource('reward-levels', RewardLevelController::class);

    // Rewards
    Route::resource('reward-levels.rewards', RewardController::class)->except(['index', 'show']);

    // Club Members
    Route::resource('members', ClubUserController::class)->only(['index', 'show']);
});
