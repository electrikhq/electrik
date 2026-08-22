<?php

namespace Electrik\Livewire\Auth;

use Electrik\Actions\Auth\ResetUserPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.guest')]
#[Title('Reset password')]
class ResetPassword extends Component
{
    #[Locked]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->string('email');
    }

    public function resetPassword(ResetUserPassword $resetUserPassword): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user) use ($resetUserPassword) {
                $resetUserPassword->execute($user, $this->password);

                Auth::login($user);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        session()->regenerate();

        $this->redirect(config('electrik.auth.home', '/dashboard'), navigate: true);
    }

    public function render()
    {
        return view('electrik::livewire.auth.reset-password');
    }
}
