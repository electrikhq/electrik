<?php

namespace Electrik\Livewire\Settings;

use Electrik\Support\Timezones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Profile')]
class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $timezone = 'UTC';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->timezone = (string) ($user->timezone ?: config('app.timezone', 'UTC'));
    }

    public function save(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->getAuthIdentifier()),
            ],
            'timezone' => ['required', 'string', 'timezone:all'],
        ]);

        $emailChanged = strcasecmp((string) $user->email, $validated['email']) !== 0;

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'timezone' => $validated['timezone'],
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged && method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
        }

        session()->flash('status', __('Profile updated.'));
    }

    public function render()
    {
        return view('electrik::livewire.settings.profile', [
            'timezones' => Timezones::options(),
        ]);
    }
}
