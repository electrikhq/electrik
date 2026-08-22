<?php

namespace Electrik\Livewire\Auth;

use Electrik\Actions\Auth\CreateNewUser;
use Electrik\Support\Onboarding;
use Electrik\Support\TeamInviteContext;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.guest')]
#[Title('Create account')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $inviteLockedEmail = false;

    public ?string $inviteTeamName = null;

    public function mount(): void
    {
        $invite = TeamInviteContext::findValidAcceptInvite(TeamInviteContext::peekToken());

        if ($invite) {
            $this->email = $invite->email;
            $this->inviteLockedEmail = true;
            $this->inviteTeamName = $invite->team->name;
        }
    }

    public function register(CreateNewUser $createNewUser): void
    {
        abort_unless(config('electrik.auth.registration', true), 404);

        $invite = TeamInviteContext::findValidAcceptInvite(TeamInviteContext::peekToken());

        if ($invite) {
            $this->email = $invite->email;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($invite && strcasecmp($validated['email'], $invite->email) !== 0) {
            throw ValidationException::withMessages([
                'email' => __('Register with the email this invitation was sent to.'),
            ]);
        }

        $user = $createNewUser->execute($validated);

        Auth::login($user);

        session()->regenerate();

        if ($user instanceof MustVerifyEmail && config('electrik.auth.email_verification', true)) {
            $this->redirect(route('verification.notice'), navigate: true);

            return;
        }

        if ($token = TeamInviteContext::peekToken()) {
            $this->redirect(route('teams.invitations.accept', $token), navigate: true);

            return;
        }

        $this->redirect(Onboarding::homePath(), navigate: true);
    }

    public function render()
    {
        return view('electrik::livewire.auth.register');
    }
}
