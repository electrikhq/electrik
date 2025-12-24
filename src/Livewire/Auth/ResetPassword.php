<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\ResetPassword as ResetPasswordAction;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Component
{
    public $token;
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:8|confirmed',
    ];

    public function mount($token)
    {
        $this->token = $token;
    }

    public function resetPassword()
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $action = new ResetPasswordAction();
                $action->execute($user, $password);
                
                Password::deleteToken($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', __($status));
            return redirect()->route('login');
        }

        $this->addError('email', __($status));
    }

    public function render()
    {
        return view('livewire.auth.reset-password')
            ->layout('layouts.auth');
    }
}

