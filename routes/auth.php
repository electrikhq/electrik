<?php

use Electrik\Http\Controllers\Auth\LogoutController;
use Electrik\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('login', 'electrik.auth.login')->name('login');

    if (config('electrik.auth.registration', true)) {
        Route::livewire('register', 'electrik.auth.register')->name('register');
    }

    Route::livewire('forgot-password', 'electrik.auth.forgot-password')->name('password.request');
    Route::livewire('reset-password/{token}', 'electrik.auth.reset-password')->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');

    if (config('electrik.auth.email_verification', true)) {
        Route::livewire('verify-email', 'electrik.auth.verify-email')->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
    }
});
