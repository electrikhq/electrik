<?php

use Electrik\Http\Middleware\EnsureOnboardingComplete;
use Electrik\Http\Middleware\EnsureSubscriptionActive;
use Electrik\Http\Middleware\EnsureTeamSelected;
use Electrik\Http\Middleware\SetPermissionsTeamId;
use Illuminate\Support\Facades\Route;

Route::livewire('pricing', 'electrik.pricing')->name('pricing');

Route::middleware(['auth', 'verified', SetPermissionsTeamId::class, EnsureTeamSelected::class])
    ->group(function () {
        Route::livewire('onboarding', 'electrik.onboarding')->name('onboarding');
    });

Route::middleware(['auth', 'verified', SetPermissionsTeamId::class, EnsureTeamSelected::class, EnsureOnboardingComplete::class, EnsureSubscriptionActive::class])
    ->group(function () {
        Route::livewire('dashboard', 'electrik.dashboard')->name('dashboard');
    });
