<?php

declare(strict_types=1);

use App\Http\Controllers\ClubAdmins\AdminInvitationController;
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

    Route::resource('clubs.admin-invitations', AdminInvitationController::class)->only(['store', 'destroy']);
    Route::post('clubs/{club}/admin-invitations/{adminInvitation}/resend', [AdminInvitationController::class, 'resend'])
        ->name('clubs.admin-invitations.resend');
});

Route::get('clubs/{club}/admin-invitations/{adminInvitation}/accept', [AdminInvitationController::class, 'accept'])
    ->name('clubs.admin-invitations.accept')
    ->middleware('signed');
