<?php

namespace Electrik\Livewire\Teams;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mpociot\Teamwork\Exceptions\UserNotInTeamException;
use Spatie\Permission\PermissionRegistrar;

#[Layout('electrik::components.layouts.app')]
#[Title('Teams')]
class Index extends Component
{
    public function switch(int $teamId): void
    {
        try {
            auth()->user()->switchTeam($teamId);
        } catch (UserNotInTeamException $e) {
            $this->addError('team', $e->getMessage());

            return;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        $this->redirect(config('electrik.auth.home', '/dashboard'), navigate: true);
    }

    public function render()
    {
        return view('electrik::livewire.teams.index', [
            'teams' => auth()->user()->teams()->orderBy('name')->get(),
            'currentTeamId' => auth()->user()->current_team_id,
        ]);
    }
}
