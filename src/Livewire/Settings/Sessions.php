<?php

namespace Electrik\Livewire\Settings;

use Electrik\Support\SessionList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Sessions')]
class Sessions extends Component
{
    public string $password = '';

    public function logoutSession(string $sessionId): void
    {
        if ($sessionId === session()->getId()) {
            return;
        }

        SessionList::deleteForUser(Auth::user(), $sessionId);

        session()->flash('status', __('Session revoked.'));
    }

    public function logoutOtherSessions(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (! Hash::check($this->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        SessionList::deleteOthers($user);

        $this->reset('password');

        session()->flash('status', __('Other sessions signed out.'));
    }

    public function render()
    {
        return view('electrik::livewire.settings.sessions', [
            'sessions' => SessionList::forUser(Auth::user()),
            'usesDatabaseSessions' => config('session.driver') === 'database',
        ]);
    }
}
