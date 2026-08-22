<?php

namespace Electrik\Livewire\Auth;

use Electrik\Actions\Auth\AttemptLogin;
use Electrik\Support\TeamInviteContext;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.guest')]
#[Title('Sign in')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

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

    public function login(AttemptLogin $attemptLogin): void
    {
        $invite = TeamInviteContext::findValidAcceptInvite(TeamInviteContext::peekToken());
        if ($invite) {
            $this->email = $invite->email;
        }

        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($invite && strcasecmp($this->email, $invite->email) !== 0) {
            throw ValidationException::withMessages([
                'email' => __('Sign in with the email this invitation was sent to.'),
            ]);
        }

        $this->ensureIsNotRateLimited();

        $ok = $attemptLogin->execute([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember);

        if (! $ok) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        session()->regenerate();

        if ($token = TeamInviteContext::peekToken()) {
            $this->redirect(route('teams.invitations.accept', $token), navigate: true);

            return;
        }

        $this->redirectIntended(default: config('electrik.auth.home', '/dashboard'), navigate: true);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    public function render()
    {
        return view('electrik::livewire.auth.login');
    }
}
