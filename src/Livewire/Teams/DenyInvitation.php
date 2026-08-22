<?php

namespace Electrik\Livewire\Teams;

use Electrik\Support\TeamInviteContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mpociot\Teamwork\Facades\Teamwork;

#[Layout('electrik::components.layouts.guest')]
#[Title('Decline invitation')]
class DenyInvitation extends Component
{
    #[Locked]
    public string $token = '';

    public ?string $error = null;

    public ?string $teamName = null;

    public bool $declined = false;

    public function mount(string $token): void
    {
        $this->token = $token;

        $invite = Teamwork::getInviteFromDenyToken($token);

        if (! $invite || TeamInviteContext::isExpired($invite)) {
            $this->error = $invite && TeamInviteContext::isExpired($invite)
                ? __('This invitation has expired.')
                : __('This invitation is invalid or has already been used.');

            return;
        }

        $this->teamName = $invite->team->name;
    }

    public function deny(): void
    {
        $invite = Teamwork::getInviteFromDenyToken($this->token);

        if (! $invite || TeamInviteContext::isExpired($invite)) {
            $this->error = __('This invitation is invalid or has expired.');

            return;
        }

        Teamwork::denyInvite($invite);
        $this->declined = true;
        $this->teamName = $invite->team->name ?? $this->teamName;
    }

    public function render()
    {
        return view('electrik::livewire.teams.deny-invitation');
    }
}
