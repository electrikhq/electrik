<?php

namespace Electrik\Livewire\Billing;

use Electrik\Actions\Billing\SyncCheckoutSubscription;
use Electrik\Concerns\ResolvesTeamBilling;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Billing')]
class Index extends Component
{
    use ResolvesTeamBilling;

    public function mount(SyncCheckoutSubscription $syncCheckout): void
    {
        $team = $this->currentTeamOrRedirect();

        if (! $team) {
            return;
        }

        abort_unless(
            auth()->user()->can('billing.view') || auth()->user()->isOwnerOfTeam($team),
            403
        );

        if (request()->query('checkout') === 'success') {
            $sessionId = (string) request()->query('session_id', '');

            if ($sessionId !== '') {
                try {
                    $syncCheckout->execute($team, $sessionId);
                    session()->flash('status', __('Checkout completed. Your subscription is active.'));
                } catch (\Throwable $e) {
                    report($e);
                    session()->flash('status', __('Checkout completed. If your plan does not appear, wait a moment or refresh — Stripe may still be confirming.'));
                }
            } else {
                session()->flash('status', __('Checkout completed. If your plan does not appear yet, refresh in a few seconds.'));
            }
        }
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $subscription = $this->teamSubscription($team);

        return view('electrik::livewire.billing.index', [
            'team' => $team,
            'subscription' => $subscription,
            'plan' => $this->planForSubscription($subscription),
        ]);
    }
}
