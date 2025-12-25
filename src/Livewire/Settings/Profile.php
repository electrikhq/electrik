<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $name = '';
    public $email = '';
    public $timezone = 'UTC';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->timezone = $user->timezone ?? 'UTC';
    }

    public function update()
    {
        $user = Auth::user();
        
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'timezone' => 'required|string',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'timezone' => $this->timezone,
        ]);

        session()->flash('message', 'Profile updated successfully.');
    }

    public function render()
    {
        return view('livewire.settings.profile')
            ->layout('layouts.app', [
                'title' => 'Profile Settings',
            ]);
    }
}

