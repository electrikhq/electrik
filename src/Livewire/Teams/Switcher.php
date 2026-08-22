<?php

namespace Electrik\Livewire\Teams;

use Livewire\Component;
use Mpociot\Teamwork\Exceptions\UserNotInTeamException;
use Spatie\Permission\PermissionRegistrar;

class Switcher extends Component
{
    public function switch(int $teamId): void
    {
        try {
            auth()->user()->switchTeam($teamId);
        } catch (UserNotInTeamException $e) {
            return;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        $this->redirect(config('electrik.auth.home', '/dashboard'), navigate: true);
    }

    public function render()
    {
        return view('electrik::livewire.teams.switcher', [
            'teams' => auth()->user()?->teams()->orderBy('name')->get() ?? collect(),
            'currentTeamId' => auth()->user()?->current_team_id,
        ]);
    }
}
