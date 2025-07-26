<?php

declare(strict_types=1);

use App\Http\Controllers\Clubs\AdminInvitationController;
use App\Http\Controllers\Clubs\ClubAdminController;
use App\Http\Controllers\Clubs\ClubController;
use App\Http\Controllers\Clubs\SwitchClubController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::resource('clubs', ClubController::class)->except(['show', 'index']);
    Route::post('clubs/{club}/switch', SwitchClubController::class)
        ->name('clubs.switch');
    Route::get('clubs/{club}/admins', [ClubAdminController::class, 'index'])
        ->name('clubs.admins.index');

    Route::post('clubs/{club}/admin-invitations', [AdminInvitationController::class, 'store'])
        ->name('clubs.admin-invitations.store');

});

Route::get('clubs/{club}/admin-invitations/{adminInvitation}/accept', [AdminInvitationController::class, 'accept'])
    ->name('clubs.admin-invitations.accept')
    ->middleware('signed');
