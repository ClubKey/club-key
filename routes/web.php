<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Webhooks\VonageInboundWebhookController;
use App\Http\Controllers\Webhooks\VonageStatusWebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('webhooks/vonage')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function (): void {
        Route::post('inbound', VonageInboundWebhookController::class)->name('webhooks.vonage.inbound');
        Route::post('status', VonageStatusWebhookController::class)->name('webhooks.vonage.status');
    });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Wildcard route for clubs, so this must be last.
require __DIR__.'/clubs.php';
