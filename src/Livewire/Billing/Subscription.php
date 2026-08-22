<?php

namespace Electrik\Livewire\Billing;

use Electrik\Actions\Billing\CancelSubscription;
use Electrik\Actions\Billing\ResumeSubscription;
use Electrik\Concerns\ResolvesTeamBilling;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Subscription')]
class Subscription extends Component
{
    use ResolvesTeamBilling;

    public function mount(): void
    {
        $this->currentTeamOrRedirect();
        abort_unless(
            auth()->user()->can('billing.view') || auth()->user()->isOwnerOfTeam(auth()->user()->currentTeam),
            403
        );
    }

    public function cancel(CancelSubscription $cancelSubscription): void
    {
        $team = $this->currentTeamOrRedirect();

        if (! $team) {
            return;
        }

        abort_unless(
            auth()->user()->can('billing.manage') || auth()->user()->isOwnerOfTeam($team),
            403
        );

        try {
            $cancelSubscription->execute($team);
            session()->flash('status', __('Subscription will end at the close of the billing period.'));
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function resume(ResumeSubscription $resumeSubscription): void
    {
        $team = $this->currentTeamOrRedirect();

        if (! $team) {
            return;
        }

        abort_unless(
            auth()->user()->can('billing.manage') || auth()->user()->isOwnerOfTeam($team),
            403
        );

        try {
            $resumeSubscription->execute($team);
            session()->flash('status', __('Subscription resumed.'));
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $subscription = $this->teamSubscription($team);

        return view('electrik::livewire.billing.subscription', [
            'team' => $team,
            'subscription' => $subscription,
            'plan' => $this->planForSubscription($subscription),
        ]);
    }
}
