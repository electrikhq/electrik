<?php

namespace Electrik\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function render()
    {
        return view('electrik::livewire.settings.security');
    }
}
