<?php

namespace Electrik\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.guest')]
#[Title('Forgot password')]
class ForgotPassword extends Component
{
    public string $email = '';

    public ?string $status = null;

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->status = __($status);
    }

    public function render()
    {
        return view('electrik::livewire.auth.forgot-password');
    }
}
