<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPassword extends Component
{
    public $email = '';
    public $status = '';

    protected $rules = [
        'email' => 'required|email',
    ];

    public function sendResetLink()
    {
        $this->validate();

        // Rate limiting
        $key = 'password.reset.'.$this->email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', 'Too many attempts. Please try again later.');
            return;
        }

        $status = Password::sendResetLink(['email' => $this->email]);

        RateLimiter::hit($key);

        if ($status === Password::RESET_LINK_SENT) {
            $this->status = __($status);
            $this->reset('email');
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('layouts.auth');
    }
}

