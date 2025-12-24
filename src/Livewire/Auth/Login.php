<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\LoginUser;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $error = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        // Rate limiting
        $key = 'login.'.$this->email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Too many login attempts. Please try again later.');
            return;
        }

        $action = new LoginUser();
        $success = $action->execute([
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if ($success && $this->remember) {
            auth()->user()->setRememberToken(\Illuminate\Support\Str::random(60));
            auth()->user()->save();
        }

        if ($success) {
            RateLimiter::clear($key);
            session()->regenerate();
            return redirect()->intended(route('dashboard.index'));
        }

        RateLimiter::hit($key);
        $this->addError('email', 'These credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.auth');
    }
}

