<?php

namespace Electrik\Livewire\Settings;

use Electrik\Support\SessionList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;

#[Layout('electrik::components.layouts.app')]
#[Title('Sessions')]
class Sessions extends Component
{
    use WithPagination;

    public string $password = '';

    public string $renamingSessionId = '';

    public string $sessionLabel = '';

    #[Url]
    public string $authFilter = '';

    public function startRename(string $sessionId): void
    {
        $this->renamingSessionId = $sessionId;
        $labels = SessionList::labelsForUser(Auth::user());
        $this->sessionLabel = $labels[$sessionId] ?? '';
    }

    public function saveSessionLabel(): void
    {
        $this->validate([
            'renamingSessionId' => ['required', 'string'],
            'sessionLabel' => ['nullable', 'string', 'max:120'],
        ]);

        SessionList::setLabel(Auth::user(), $this->renamingSessionId, $this->sessionLabel);
        $this->reset('renamingSessionId', 'sessionLabel');

        session()->flash('status', __('Device name saved.'));
    }

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

    public function updatingAuthFilter(): void
    {
        $this->resetPage('authPage');
    }

    public function render()
    {
        $user = Auth::user();
        $authLogEnabled = $this->authenticationLogEnabled($user);
        $authenticationLogs = collect();

        if ($authLogEnabled) {
            $query = $user->authentications();

            if ($this->authFilter === 'success') {
                $query->where('login_successful', true);
            } elseif ($this->authFilter === 'failed') {
                $query->where('login_successful', false);
            } elseif ($this->authFilter === 'suspicious') {
                $query->where('is_suspicious', true);
            }

            $authenticationLogs = $query->paginate(15, pageName: 'authPage');
        }

        return view('electrik::livewire.settings.sessions', [
            'sessions' => SessionList::forUser($user),
            'usesDatabaseSessions' => config('session.driver') === 'database',
            'authLogEnabled' => $authLogEnabled,
            'authenticationLogs' => $authenticationLogs,
        ]);
    }

    protected function authenticationLogEnabled(mixed $user): bool
    {
        if (! class_exists(\Rappasoft\LaravelAuthenticationLog\LaravelAuthenticationLogServiceProvider::class)) {
            return false;
        }

        $table = config('authentication-log.table_name', 'authentication_log');

        if (! Schema::hasTable($table)) {
            return false;
        }

        return is_object($user) && in_array(AuthenticationLoggable::class, class_uses_recursive($user), true);
    }
}
