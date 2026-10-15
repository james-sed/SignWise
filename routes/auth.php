<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::controller(AuthController::class)->group(function () {

        Route::get('/register/create', 'register_create')->name('show.register');

        Route::post('/register/store', 'register_store');

        Route::get('/registration_verify/{token}/{email}', 'registration_verify')->name('registration_verify');

        Route::get('/login/create', 'login_create')->name('login');

        Route::middleware('throttle:login')->post('/login/store', 'login_store');

        Route::get('/forgotPassword', 'forgetPassword')->name('forget_password');

        Route::post('/forgot_Password_submit', 'forget_Password_Submit')->name('forget_password_submit');

        Route::get('/reset_password/{token}/{email}', 'user_reset_password')->name('reset_password');

        Route::post('/reset_password/{token}/{email}', 'reset_password_submit')->name('reset_password_submit');
    });
});


Route::middleware('auth')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout')->name('logout');
    });
});
