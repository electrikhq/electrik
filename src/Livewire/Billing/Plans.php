<?php

namespace App\Livewire\Billing;

use App\Models\StripePlan;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Plans extends Component
{
    public function render()
    {
        $team = Auth::user()->currentTeam;
        
        if (!$team) {
            return redirect()->route('teams.index');
        }
        
        $plans = StripePlan::with('product')
            ->orderBy('price')
            ->get()
            ->groupBy('product.name');

        $currentPlan = null;
        $subscription = $team->subscription(config('electrik.default_subscription_name', 'electrik'));
        
        if ($subscription) {
            $subscriptionItem = $subscription->items()->first();
            if ($subscriptionItem) {
                $currentPlan = StripePlan::where('stripe_price_id', $subscriptionItem->stripe_price)->first();
            }
        }

        return view('livewire.billing.plans', [
            'plans' => $plans,
            'currentPlan' => $currentPlan,
        ])
            ->layout('layouts.app', [
                'title' => 'Subscription Plans',
            ]);
    }
}

