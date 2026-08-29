<?php

namespace Electrik\Livewire\Auth;

use Electrik\Support\Onboarding;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.guest')]
#[Title('Verify email')]
class VerifyEmail extends Component
{
    public ?string $status = null;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user?->hasVerifiedEmail()) {
            $this->redirect(Onboarding::homePath(), navigate: true);
        }
    }

    public function resend(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirect(Onboarding::homePath(), navigate: true);

            return;
        }

        $user->sendEmailVerificationNotification();

        $this->status = __('A new verification link has been sent to your email address.');
    }

    public function render()
    {
        return view('electrik::livewire.auth.verify-email');
    }
}
