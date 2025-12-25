<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Security extends Component
{
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'current_password' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ];

    public function updatePassword()
    {
        $this->validate();

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        // Update password (Laravel will automatically hash it due to password casting)
        $user->update([
            'password' => $this->password,
        ]);

        // Clear form
        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('message', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.settings.security')
            ->layout('layouts.app', [
                'title' => 'Security Settings',
            ]);
    }
}

