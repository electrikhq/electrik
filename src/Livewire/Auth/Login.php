<?php

namespace Electrik\Livewire\Auth;

use Electrik\Support\Auth as ElectrikAuth;
use Electrik\Support\Onboarding;
use Electrik\Support\TeamInviteContext;
use Electrik\Support\TwoFactorAuth;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public bool $requiresTwoFactor = false;

    public string $twoFactorCode = '';

    public function mount(): void
    {
        $invite = TeamInviteContext::findValidAcceptInvite(TeamInviteContext::peekToken());

        if ($invite) {
            $this->email = $invite->email;
            $this->inviteLockedEmail = true;
            $this->inviteTeamName = $invite->team->name;
        }

        $this->requiresTwoFactor = session()->has('login.two_factor.id');
    }

    public function login(): void
    {
        if ($this->requiresTwoFactor) {
            $this->verifyTwoFactor();

            return;
        }

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

        $userModel = config('auth.providers.users.model');
        $user = is_string($userModel) ? $userModel::query()->where('email', $this->email)->first() : null;

        if (! $user || ! Hash::check($this->password, $user->password)) {
            if ($user) {
                // Keep password check separate from Auth::attempt so 2FA can gate login;
                // still fire Failed so rappasoft/laravel-authentication-log records it.
                event(new Failed('web', $user, [
                    'email' => $this->email,
                    'password' => $this->password,
                ]));
            }

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        if (ElectrikAuth::isSuspended($user)) {
            throw ValidationException::withMessages([
                'email' => __('This account has been suspended.'),
            ]);
        }

        if (TwoFactorAuth::enabled($user)) {
            session([
                'login.two_factor.id' => $user->getAuthIdentifier(),
                'login.two_factor.remember' => $this->remember,
            ]);
            $this->requiresTwoFactor = true;
            $this->reset('password');

            return;
        }

        RateLimiter::clear($this->throttleKey());
        Auth::login($user, $this->remember);
        session()->regenerate();

        $this->completeLogin();
    }

    public function verifyTwoFactor(): void
    {
        $this->validate([
            'twoFactorCode' => ['required', 'string'],
        ]);

        $userId = session('login.two_factor.id');
        $userModel = config('auth.providers.users.model');
        $user = is_string($userModel) ? $userModel::query()->find($userId) : null;

        if (! $user) {
            session()->forget(['login.two_factor.id', 'login.two_factor.remember']);
            $this->requiresTwoFactor = false;

            throw ValidationException::withMessages([
                'twoFactorCode' => __('Your session expired. Please sign in again.'),
            ]);
        }

        if (ElectrikAuth::isSuspended($user)) {
            session()->forget(['login.two_factor.id', 'login.two_factor.remember']);
            $this->requiresTwoFactor = false;

            throw ValidationException::withMessages([
                'twoFactorCode' => __('This account has been suspended.'),
            ]);
        }

        $code = trim($this->twoFactorCode);
        $verified = TwoFactorAuth::verify($user, $code);

        if (! $verified && str_contains($code, '-')) {
            $verified = $this->attemptRecoveryCode($user, $code);
        }

        if (! $verified) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'twoFactorCode' => __('The verification code is invalid.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        $remember = (bool) session('login.two_factor.remember', false);
        session()->forget(['login.two_factor.id', 'login.two_factor.remember']);

        Auth::login($user, $remember);
        session()->regenerate();

        $this->completeLogin();
    }

    protected function attemptRecoveryCode(object $user, string $code): bool
    {
        $codes = TwoFactorAuth::recoveryCodes($user);
        $normalized = strtoupper(str_replace(' ', '', $code));

        foreach ($codes as $index => $stored) {
            if (hash_equals(strtoupper(str_replace(' ', '', $stored)), $normalized)) {
                unset($codes[$index]);
                $user->forceFill([
                    'two_factor_recovery_codes' => TwoFactorAuth::encryptRecoveryCodes(array_values($codes)),
                ])->save();

                return true;
            }
        }

        return false;
    }

    protected function completeLogin(): void
    {
        if ($token = TeamInviteContext::peekToken()) {
            $this->redirect(route('teams.invitations.accept', $token), navigate: true);

            return;
        }

        $this->redirectIntended(default: Onboarding::homePath(), navigate: true);
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
