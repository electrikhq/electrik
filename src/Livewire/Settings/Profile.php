<?php

namespace Electrik\Livewire\Settings;

use Electrik\Support\Timezones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('electrik::components.layouts.app')]
#[Title('Profile')]
class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $timezone = 'UTC';

    public $avatar;

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

    public function updateAvatar(): void
    {
        $this->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();
        $path = $this->avatar->store('avatars/'.$user->getAuthIdentifier(), 'public');

        if ($user->avatar_path ?? null) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_path);
        }

        $user->forceFill(['avatar_path' => $path])->save();
        $this->reset('avatar');

        session()->flash('status', __('Profile photo updated.'));
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar_path ?? null) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_path);
            $user->forceFill(['avatar_path' => null])->save();
        }

        session()->flash('status', __('Profile photo removed.'));
    }

    public function avatarUrl(): ?string
    {
        $user = Auth::user();

        if (! ($user->avatar_path ?? null)) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path);
    }

    public function render()
    {
        return view('electrik::livewire.settings.profile', [
            'timezones' => Timezones::options(),
        ]);
    }
}
