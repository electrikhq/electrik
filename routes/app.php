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

Route::middleware(['auth', 'verified', SetPermissionsTeamId::class, EnsureTeamSelected::class, EnsureOnboardingComplete::class, EnsureSubscriptionActive::class, \Electrik\Http\Middleware\EnsureTeamIpAllowed::class])
    ->group(function () {
        Route::livewire('dashboard', 'electrik.dashboard')->name('dashboard');

        if (config('electrik.sample.projects', true)) {
            Route::livewire('clients', 'electrik.clients.index')->name('clients.index');
            Route::livewire('projects', 'electrik.projects.index')->name('projects.index');
            Route::livewire('projects/{project}', 'electrik.projects.show')->name('projects.show');
        }
    });
