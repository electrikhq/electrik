<?php

namespace Electrik\Livewire;

use Electrik\Models\StripePlan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.guest')]
#[Title('Pricing')]
class Pricing extends Component
{
    public function render()
    {
        $planModel = config('electrik.billing.plan_model', StripePlan::class);

        $plans = $planModel::query()
            ->with('product')
            ->orderBy('price')
            ->get()
            ->groupBy(fn (StripePlan $plan) => $plan->product?->name ?? __('Plans'));

        return view('electrik::livewire.pricing', [
            'plans' => $plans,
        ]);
    }
}
