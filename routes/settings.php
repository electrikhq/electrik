<?php

use Electrik\Http\Middleware\EnsureOnboardingComplete;
use Electrik\Http\Middleware\EnsureTeamSelected;
use Electrik\Http\Middleware\SetPermissionsTeamId;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', SetPermissionsTeamId::class, EnsureTeamSelected::class, EnsureOnboardingComplete::class])->group(function () {
    Route::livewire('settings/profile', 'electrik.settings.profile')->name('settings.profile');
    Route::livewire('settings/security', 'electrik.settings.security')->name('settings.security');
    Route::livewire('settings/api-tokens', 'electrik.settings.api-tokens')->name('settings.api-tokens');
});
