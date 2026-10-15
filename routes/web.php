<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    // Forgot password: the page plus the JSON endpoint that emails the link.
    Route::get('/forgot-password', function () {
        return response()->file(public_path('forgot-password.html'));
    })->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    // Reset password: the page plus the JSON validate/reset endpoints. The
    // page is served with a strict Referrer-Policy so the token never leaks
    // through a Referer header.
    Route::get('/reset-password', function () {
        return response()->file(
            public_path('reset-password.html'),
            ['Referrer-Policy' => 'no-referrer']
        );
    })->name('password.reset');

    Route::post('/reset-password/validate', [ResetPasswordController::class, 'validateToken'])
        ->middleware('throttle:6,1')
        ->name('password.validate');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->middleware('throttle:6,1')
        ->name('password.update');
});

require __DIR__.'/auth.php';
