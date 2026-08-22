<?php

namespace Electrik\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead(string $id): void
    {
        auth()->user()->notifications()->whereKey($id)->first()?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $user = auth()->user();

        return view('electrik::livewire.notification-bell', [
            'notifications' => $user->notifications()->limit(10)->get(),
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }
}
