<?php

namespace Electrik\Livewire\Billing;

use Electrik\Concerns\ResolvesTeamBilling;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Payment methods')]
class PaymentMethods extends Component
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

    public function openPortal()
    {
        $team = $this->currentTeamOrRedirect();

        if (! $team) {
            return;
        }

        abort_unless(
            auth()->user()->can('billing.manage') || auth()->user()->isOwnerOfTeam($team),
            403
        );

        $team->createOrGetStripeCustomer();

        return $this->redirect($team->billingPortalUrl(route('billing.payment-methods', absolute: true)));
    }

    public function setDefault(string $paymentMethodId): void
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
            $team->updateDefaultPaymentMethod($paymentMethodId);
            session()->flash('status', __('Default payment method updated.'));
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function remove(string $paymentMethodId): void
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
            $team->findPaymentMethod($paymentMethodId)?->delete();
            session()->flash('status', __('Payment method removed.'));
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;

        return view('electrik::livewire.billing.payment-methods', [
            'team' => $team,
            'paymentMethods' => $team?->hasStripeId() ? $team->paymentMethods() : collect(),
            'defaultPaymentMethod' => $team?->hasStripeId() ? $team->defaultPaymentMethod() : null,
        ]);
    }
}
