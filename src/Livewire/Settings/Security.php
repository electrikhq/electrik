<?php

namespace Electrik\Livewire\Settings;

use Electrik\Support\TwoFactorAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Security')]
class Security extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $showSetup = false;

    public ?string $setupSecret = null;

    public string $confirmationCode = '';

    /** @var list<string> */
    public array $recoveryCodes = [];

    public string $passkeyName = '';

    public function mount(): void
    {
        abort_unless(
            Schema::hasColumn('users', 'two_factor_secret')
                || $this->passkeysEnabled(),
            404
        );
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('The current password is incorrect.'),
            ]);
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('status', __('Password updated.'));
    }

    public function beginTwoFactorSetup(): void
    {
        abort_unless(Schema::hasColumn('users', 'two_factor_secret'), 404);

        $this->setupSecret = TwoFactorAuth::generateSecret();
        $this->showSetup = true;
        $this->reset('confirmationCode', 'recoveryCodes');
    }

    public function confirmTwoFactorSetup(): void
    {
        abort_unless(Schema::hasColumn('users', 'two_factor_secret'), 404);

        $this->validate([
            'confirmationCode' => ['required', 'string', 'size:6'],
        ]);

        abort_unless($this->setupSecret, 422);

        $user = Auth::user();
        $valid = TwoFactorAuth::verifySecret($this->setupSecret, $this->confirmationCode);

        if (! $valid) {
            throw ValidationException::withMessages([
                'confirmationCode' => __('The verification code is invalid.'),
            ]);
        }

        $recoveryCodes = TwoFactorAuth::generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => TwoFactorAuth::encryptSecret($this->setupSecret),
            'two_factor_recovery_codes' => TwoFactorAuth::encryptRecoveryCodes($recoveryCodes),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->recoveryCodes = $recoveryCodes;
        $this->showSetup = false;
        $this->setupSecret = null;
        $this->reset('confirmationCode');

        session()->flash('status', __('Two-factor authentication enabled.'));
    }

    public function disableTwoFactor(): void
    {
        abort_unless(Schema::hasColumn('users', 'two_factor_secret'), 404);

        $this->validate([
            'current_password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (! Hash::check($this->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('The current password is incorrect.'),
            ]);
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->reset(['current_password', 'recoveryCodes', 'showSetup', 'setupSecret']);

        session()->flash('status', __('Two-factor authentication disabled.'));
    }

    public function deletePasskey(string $passkeyId): void
    {
        abort_unless($this->passkeysEnabled(), 404);

        $user = Auth::user();

        abort_unless(method_exists($user, 'passkeys'), 404);

        $passkey = $user->passkeys()->whereKey($passkeyId)->firstOrFail();
        $passkey->delete();

        session()->flash('status', __('Passkey removed.'));
    }

    public function passkeyRegistered(): void
    {
        $this->reset('passkeyName');
        session()->flash('status', __('Passkey added.'));
    }

    protected function passkeysEnabled(): bool
    {
        return class_exists(\Laravel\Passkeys\PasskeysServiceProvider::class)
            && Schema::hasTable('passkeys');
    }

    public function render()
    {
        $user = Auth::user();
        $passkeysEnabled = $this->passkeysEnabled();
        $twoFactorAvailable = Schema::hasColumn('users', 'two_factor_secret');

        return view('electrik::livewire.settings.security', [
            'twoFactorAvailable' => $twoFactorAvailable,
            'twoFactorEnabled' => $twoFactorAvailable && TwoFactorAuth::enabled($user),
            'qrInline' => ($this->showSetup && $this->setupSecret)
                ? TwoFactorAuth::qrInline($user, $this->setupSecret)
                : null,
            'passkeysEnabled' => $passkeysEnabled,
            'passkeys' => $passkeysEnabled && method_exists($user, 'passkeys')
                ? $user->passkeys()->latest()->get()
                : collect(),
        ]);
    }
}
