<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'electrik.operator'])->group(function () {
    Route::livewire('ops', 'electrik.ops.dashboard')->name('ops.dashboard');
    Route::livewire('ops/users', 'electrik.ops.users')->name('ops.users');
    Route::livewire('ops/teams', 'electrik.ops.teams')->name('ops.teams');
    Route::livewire('ops/webhooks', 'electrik.ops.webhooks')->name('ops.webhooks');
    Route::livewire('ops/failed-jobs', 'electrik.ops.failed-jobs')->name('ops.failed-jobs');
    Route::livewire('ops/plans', 'electrik.ops.plans')->name('ops.plans');
    Route::livewire('ops/mail-preview', 'electrik.ops.mail-preview')->name('ops.mail-preview');

    Route::livewire('ops/announcements', 'electrik.ops.announcements.index')->name('ops.announcements.index');
    Route::livewire('ops/announcements/create', 'electrik.ops.announcements.form')->name('ops.announcements.create');
    Route::livewire('ops/announcements/{announcement}/edit', 'electrik.ops.announcements.form')->name('ops.announcements.edit');
});
