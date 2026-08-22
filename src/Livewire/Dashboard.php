<?php

namespace Electrik\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $team = $user?->currentTeam;

        return view('electrik::livewire.dashboard', [
            'user' => $user,
            'team' => $team,
            'subscription' => $team
                ? $team->subscription(config('electrik.billing.subscription_name', 'electrik'))
                : null,
        ]);
    }
}
