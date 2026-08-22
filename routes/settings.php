<?php

use Electrik\Http\Middleware\SetPermissionsTeamId;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', SetPermissionsTeamId::class])->group(function () {
    Route::livewire('settings/profile', 'electrik.settings.profile')->name('settings.profile');
    Route::livewire('settings/security', 'electrik.settings.security')->name('settings.security');
});
