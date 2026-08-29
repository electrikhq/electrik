<?php

use Electrik\Http\Middleware\EnsureOnboardingComplete;
use Electrik\Http\Middleware\EnsureTeamSelected;
use Electrik\Http\Middleware\SetPermissionsTeamId;
use Illuminate\Support\Facades\Route;

// Public invitation links (guest may open; auth enforced inside the accept flow).
Route::livewire('teams/invitations/{token}', 'electrik.teams.accept-invitation')
    ->name('teams.invitations.accept');
Route::livewire('teams/invitations/{token}/deny', 'electrik.teams.deny-invitation')
    ->name('teams.invitations.deny');

Route::middleware(['auth', 'verified', SetPermissionsTeamId::class])->group(function () {
    Route::livewire('teams/create', 'electrik.teams.create')->name('teams.create');

    Route::middleware([EnsureTeamSelected::class, EnsureOnboardingComplete::class, \Electrik\Http\Middleware\EnsureSubscriptionActive::class, \Electrik\Http\Middleware\EnsureTeamIpAllowed::class])->group(function () {
        Route::livewire('teams', 'electrik.teams.index')->name('teams.index');
        Route::livewire('teams/{team}/settings', 'electrik.teams.settings')->name('teams.settings');
        Route::livewire('teams/{team}/members', 'electrik.teams.members')->name('teams.members');
        Route::livewire('teams/{team}/members/invite', 'electrik.teams.invite')->name('teams.members.invite');
        Route::livewire('teams/{team}/roles', 'electrik.teams.roles.index')->name('teams.roles.index');
        Route::livewire('teams/{team}/roles/create', 'electrik.teams.roles.create')
            ->middleware('electrik.plan:custom_roles')
            ->name('teams.roles.create');
        Route::livewire('teams/{team}/activity', 'electrik.teams.activity')->name('teams.activity');
        Route::livewire('teams/{team}/webhooks', 'electrik.teams.webhooks')->name('teams.webhooks');
        Route::livewire('teams/{team}/roles/{role}/edit', 'electrik.teams.roles.edit')->name('teams.roles.edit');
        Route::livewire('teams/{team}/permissions', 'electrik.teams.permissions.index')->name('teams.permissions.index');
    });
});
