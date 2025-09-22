<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\HealthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ✅ Public Webhook endpoint — no auth needed
Route::post('/webhooks/{platform}', [WebhookController::class, 'store']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    // ✅ Admin-only route to view webhook logs
    Route::get('/webhooks', [WebhookController::class, 'index'])
        ->middleware('can:view-webhooks');
    
    // ✅ Products: manual product management for logged-in user
    Route::apiResource('products', ProductController::class);
    
    // ✅ Integrations: only admin can manage
    Route::middleware(['role:admin'])->group(function () {
        Route::apiResource('integrations', IntegrationController::class);
        Route::get('/health/queue', [HealthController::class, 'queue']);
    });
});
