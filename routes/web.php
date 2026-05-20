<?php

declare(strict_types=1);

use App\Http\Controllers\Clubs\SquareIntegrationController;
use App\Http\Controllers\CommunicationSubscriptions\UnsubscribeController;
use App\Http\Controllers\CommunicationSubscriptions\VonageWebhookController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('square/oauth/callback', [SquareIntegrationController::class, 'callback'])
        ->name('square.oauth.callback');
});

// Unsubscribe routes (signed for security)
Route::get('unsubscribe/email/{subscription}', [UnsubscribeController::class, 'email'])
    ->name('unsubscribe.email')
    ->middleware('signed');
Route::get('unsubscribe/email/{subscription}/confirm', [UnsubscribeController::class, 'confirm'])
    ->name('unsubscribe.confirm')
    ->middleware('signed');
Route::post('unsubscribe/{subscription}/resubscribe', [UnsubscribeController::class, 'resubscribe'])
    ->name('unsubscribe.resubscribe')
    ->middleware('signed');

// Vonage webhook (no auth required, but should verify signature in production)
Route::post('webhooks/vonage', [VonageWebhookController::class, 'handle'])
    ->name('webhooks.vonage');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Wildcard route for clubs, so this must be last.
require __DIR__.'/clubs.php';
