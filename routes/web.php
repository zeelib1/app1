<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Cashier\Http\Controllers\WebhookController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\BlogPostController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('blog-posts', BlogPostController::class);

    // Stripe Checkout routes
    Route::get('/checkout', [StripeController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('checkout.cancel');
});

// Stripe webhook
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);

// Optional: keep this if you still need it for debugging
Route::get('/verify-stripe', [StripeController::class, 'verifyStripeConfig']);
