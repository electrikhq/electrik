<?php

namespace App\Livewire\Billing;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public function render()
    {
        $team = Auth::user()->currentTeam;
        $subscription = $team->subscription(config('electrik.default_subscription_name', 'electrik'));
        $subscriptionItem = $subscription?->items()->first();
        $plan = null;
        
        if ($subscriptionItem) {
            $plan = \App\Models\StripePlan::where('stripe_price_id', $subscriptionItem->stripe_price)->first();
        }

        return view('livewire.billing.index', [
            'team' => $team,
            'subscription' => $subscription,
            'plan' => $plan,
        ])
            ->layout('layouts.app', [
                'title' => 'Billing Overview',
            ]);
    }
}

