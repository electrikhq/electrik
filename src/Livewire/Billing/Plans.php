<?php

namespace Electrik\Livewire\Billing;

use Electrik\Actions\Billing\AddSubscriptionItem;
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

        if ($plan->is_addon) {
            session()->flash('error', __('Use Add add-on for add-on plans.'));

            return;
        }

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

    public function addAddon(int $planId, AddSubscriptionItem $addSubscriptionItem)
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
            $addSubscriptionItem->execute($team, $plan);
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        session()->flash('status', __('Add-on :plan attached.', ['plan' => $plan->name]));

        return $this->redirect(route('billing.subscription'), navigate: true);
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $subscription = $this->teamSubscription($team);
        $planModel = config('electrik.billing.plan_model', StripePlan::class);

        $basePlans = $planModel::query()
            ->with('product')
            ->where(function ($q) {
                $q->where('is_addon', false)->orWhereNull('is_addon');
            })
            ->orderBy('price')
            ->get()
            ->groupBy(fn (StripePlan $plan) => $plan->product?->name ?? __('Plans'));

        $addons = $planModel::query()
            ->with('product')
            ->where('is_addon', true)
            ->orderBy('price')
            ->get();

        return view('electrik::livewire.billing.plans', [
            'plans' => $basePlans,
            'addons' => $addons,
            'currentPlan' => $this->planForSubscription($subscription),
            'hasActiveSubscription' => (bool) ($subscription && ($subscription->active() || $subscription->onTrial() || $subscription->onGracePeriod())),
        ]);
    }
}
