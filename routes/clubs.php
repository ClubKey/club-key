<?php

declare(strict_types=1);

use App\Http\Controllers\Clubs\AdminInvitationController;
use App\Http\Controllers\Clubs\ClubAdminController;
use App\Http\Controllers\Clubs\ClubController;
use App\Http\Controllers\Clubs\ClubLocationController;
use App\Http\Controllers\Clubs\ClubUserController;
use App\Http\Controllers\Clubs\JoinClubController;
use App\Http\Controllers\Clubs\RewardController;
use App\Http\Controllers\Clubs\RewardLevelController;
use App\Http\Controllers\Clubs\SwitchClubController;
use App\Http\Controllers\Clubs\VisitController;
use App\Http\Controllers\Promotions\PromotionController;
use App\Http\Controllers\Promotions\PublicPromotionLinkController;
use App\Http\Controllers\Promotions\ResendPromotionNotification;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('clubs/{club}/admin-invitations/{adminInvitation}/accept', [AdminInvitationController::class, 'accept'])
        ->name('clubs.admin-invitations.accept')
        ->middleware('signed');

    Route::get('clubs/{club}/join', JoinClubController::class)
        ->name('clubs.join');

    Route::get('clubs/{club}/check-in-code', [ClubController::class, 'checkInCode'])
        ->name('clubs.check-in-code');

    Route::get('/visit/clubs/{club}/{clubLocation?}', [VisitController::class, 'store'])
        ->name('clubs.visit')
        ->middleware('signed');

    // The clubs show route is a wildcard route, and allows guests to view clubs.
    // It is defined at the end of this file.
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

    Route::resource('locations', ClubLocationController::class)->except('show');

    // Reward Levels
    Route::resource('reward-levels', RewardLevelController::class);

    // rewards
    Route::resource('reward-levels.rewards', RewardController::class)->except(['index', 'show']);

    // Club Members
    Route::resource('members', ClubUserController::class)->only(['index', 'show']);

    Route::resource('promotions', PromotionController::class);

    Route::post('promotions/{promotion}/resend', ResendPromotionNotification::class)->name('promotions.resend');
});

Route::get('{club}', [ClubController::class, 'show'])
    ->name('clubs.show');

Route::get('promotions/{promotion}/view', PublicPromotionLinkController::class)->name('promotions.public-link');
