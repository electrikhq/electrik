<?php

namespace App\Livewire\Billing;

use App\Actions\Billing\CancelSubscription;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Subscription extends Component
{
    public function cancel()
    {
        $team = Auth::user()->currentTeam;
        
        try {
            $action = new CancelSubscription();
            $action->execute($team);
            
            session()->flash('message', 'Subscription canceled successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $team = Auth::user()->currentTeam;
        $subscription = $team->subscription(config('electrik.default_subscription_name', 'electrik'));
        $subscriptionItem = $subscription?->items()->first();
        $plan = null;
        
        if ($subscriptionItem) {
            $plan = \App\Models\StripePlan::where('stripe_price_id', $subscriptionItem->stripe_price)->first();
        }

        return view('livewire.billing.subscription', [
            'team' => $team,
            'subscription' => $subscription,
            'plan' => $plan,
        ])
            ->layout('layouts.app', [
                'title' => 'Subscription Management',
            ]);
    }
}

