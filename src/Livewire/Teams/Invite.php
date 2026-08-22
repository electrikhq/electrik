<?php

namespace Electrik\Livewire\Teams;

use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\Team;
use Electrik\Notifications\TeamInvitationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mpociot\Teamwork\Facades\Teamwork;

#[Layout('electrik::components.layouts.app')]
#[Title('Invite member')]
class Invite extends Component
{
    use AuthorizesTeamAccess;

    #[Locked]
    public Team $team;

    public string $email = '';

    public string $role = 'member';

    public function mount(Team $team): void
    {
        $this->authorizeTeamPermission($team, 'teams.invite');
        $this->team = $team;

        $assignable = $this->assignableRoleNamesFor($team);
        if ($assignable !== [] && ! in_array($this->role, $assignable, true)) {
            $this->role = $assignable[0];
        }
    }

    public function invite(): void
    {
        $this->authorizeTeamPermission($this->team, 'teams.invite');

        $assignable = $this->assignableRoleNamesFor($this->team);

        $validated = $this->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', Rule::in($assignable)],
        ]);

        if (strcasecmp($validated['email'], (string) auth()->user()->email) === 0) {
            $this->addError('email', __('You cannot invite yourself.'));

            return;
        }

        if (Teamwork::hasPendingInvite($validated['email'], $this->team)) {
            $this->addError('email', __('This email already has a pending invitation.'));

            return;
        }

        if ($this->team->users()->where('email', $validated['email'])->exists()) {
            $this->addError('email', __('This person is already on the team.'));

            return;
        }

        $days = max(1, (int) config('electrik.teams.invite_expires_days', 7));

        Teamwork::inviteToTeam($validated['email'], $this->team, function ($invite) use ($validated, $days) {
            if (\Illuminate\Support\Facades\Schema::hasColumn($invite->getTable(), 'role')) {
                $invite->role = $validated['role'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn($invite->getTable(), 'expires_at')) {
                $invite->expires_at = now()->addDays($days);
            }
            $invite->save();

            Notification::route('mail', $invite->email)
                ->notify(new TeamInvitationNotification($invite));
        });

        session()->flash('status', __('Invitation sent.'));

        $this->redirect(route('teams.members', $this->team), navigate: true);
    }

    public function render()
    {
        return view('electrik::livewire.teams.invite', [
            'assignableRoles' => $this->assignableRoleNamesFor($this->team),
        ]);
    }
}
