<?php

namespace Electrik\Livewire\Billing;

use Electrik\Actions\Billing\CreateSubscription;
use Electrik\Concerns\ResolvesTeamBilling;
use Electrik\Models\StripePlan;
use Laravel\Cashier\Checkout;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Plans')]
class Plans extends Component
{
    use ResolvesTeamBilling;

    public function mount(): void
    {
        $this->currentTeamOrRedirect();
        abort_unless(
            auth()->user()->can('billing.view') || auth()->user()->isOwnerOfTeam(auth()->user()->currentTeam),
            403
        );

        if (request()->query('checkout') === 'cancelled') {
            session()->flash('error', __('Checkout was cancelled.'));
        }
    }

    public function subscribe(int $planId, CreateSubscription $createSubscription)
    {
        $team = $this->currentTeamOrRedirect();

        if (! $team) {
            return;
        }

        abort_unless(
            auth()->user()->can('billing.manage') || auth()->user()->isOwnerOfTeam($team),
            403
        );

        $planModel = config('electrik.billing.plan_model', StripePlan::class);
        $plan = $planModel::query()->findOrFail($planId);

        try {
            $result = $createSubscription->execute($team, $plan);
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        if ($result instanceof Checkout) {
            return $this->redirect($result->url);
        }

        session()->flash('status', __('Subscribed to :plan.', ['plan' => $plan->name]));

        return $this->redirect(route('billing.subscription'), navigate: true);
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $subscription = $this->teamSubscription($team);
        $planModel = config('electrik.billing.plan_model', StripePlan::class);

        $plans = $planModel::query()
            ->with('product')
            ->orderBy('price')
            ->get()
            ->groupBy(fn (StripePlan $plan) => $plan->product?->name ?? __('Plans'));

        return view('electrik::livewire.billing.plans', [
            'plans' => $plans,
            'currentPlan' => $this->planForSubscription($subscription),
        ]);
    }
}
