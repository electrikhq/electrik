<?php

namespace Electrik\Livewire\Teams;

use Electrik\Actions\Teams\AcceptTeamInvite;
use Electrik\Support\TeamInviteContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mpociot\Teamwork\Facades\Teamwork;

#[Layout('electrik::components.layouts.guest')]
#[Title('Accept invitation')]
class AcceptInvitation extends Component
{
    #[Locked]
    public string $token = '';

    public ?string $error = null;

    public ?string $teamName = null;

    public ?string $inviteEmail = null;

    public ?string $inviteRole = null;

    public bool $guest = false;

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->guest = ! auth()->check();

        $invite = TeamInviteContext::findValidAcceptInvite($token);

        if (! $invite) {
            $raw = Teamwork::getInviteFromAcceptToken($token);
            $this->error = $raw && TeamInviteContext::isExpired($raw)
                ? __('This invitation has expired.')
                : __('This invitation is invalid or has already been used.');

            return;
        }

        $this->teamName = $invite->team->name;
        $this->inviteEmail = $invite->email;
        $this->inviteRole = $invite->role ? ucfirst($invite->role) : __('Member');

        TeamInviteContext::stashToken($token);

        if (auth()->check()) {
            if (strcasecmp((string) auth()->user()->email, $invite->email) !== 0) {
                $this->addError('email', __('You are signed in as :current, but this invitation was sent to :invited.', [
                    'current' => auth()->user()->email,
                    'invited' => $invite->email,
                ]));

                return;
            }

            if (config('electrik.auth.email_verification', true)
                && method_exists(auth()->user(), 'hasVerifiedEmail')
                && ! auth()->user()->hasVerifiedEmail()) {
                $this->redirect(route('verification.notice'), navigate: true);

                return;
            }

            try {
                app(AcceptTeamInvite::class)->execute(auth()->user(), $invite);
                TeamInviteContext::pullToken();
                $this->redirect(config('electrik.auth.home', '/dashboard'), navigate: true);
            } catch (\Throwable $e) {
                $this->error = $e->getMessage();
            }
        }
    }

    public function accept(AcceptTeamInvite $acceptTeamInvite): void
    {
        if (! auth()->check()) {
            TeamInviteContext::stashToken($this->token);
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $invite = TeamInviteContext::findValidAcceptInvite($this->token);

        if (! $invite) {
            $this->error = __('This invitation is invalid or has expired.');

            return;
        }

        if (config('electrik.auth.email_verification', true)
            && method_exists(auth()->user(), 'hasVerifiedEmail')
            && ! auth()->user()->hasVerifiedEmail()) {
            $this->redirect(route('verification.notice'), navigate: true);

            return;
        }

        try {
            $acceptTeamInvite->execute(auth()->user(), $invite);
            TeamInviteContext::pullToken();
            $this->redirect(config('electrik.auth.home', '/dashboard'), navigate: true);
        } catch (\Throwable $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render()
    {
        return view('electrik::livewire.teams.accept-invitation');
    }
}
