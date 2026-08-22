<?php

use Electrik\Http\Middleware\EnsureSubscriptionActive;
use Electrik\Http\Middleware\EnsureTeamSelected;
use Electrik\Http\Middleware\SetPermissionsTeamId;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', SetPermissionsTeamId::class, EnsureTeamSelected::class, EnsureSubscriptionActive::class])
    ->group(function () {
        Route::livewire('dashboard', 'electrik.dashboard')->name('dashboard');
    });
