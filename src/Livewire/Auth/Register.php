<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\CreateUser;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $timezone = 'UTC';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'timezone' => 'required|string',
    ];

    public function register()
    {
        $this->validate();

        $action = new CreateUser();
        $user = $action->execute([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'timezone' => $this->timezone,
        ]);

        auth()->login($user);

        session()->regenerate();

        return redirect()->route('dashboard.index');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.auth');
    }
}

