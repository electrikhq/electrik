<?php

namespace Electrik\Livewire\Billing;

use Electrik\Concerns\ResolvesTeamBilling;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Invoices')]
class Invoices extends Component
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

    public function download(string $invoiceId)
    {
        $team = $this->currentTeamOrRedirect();

        if (! $team) {
            return;
        }

        abort_unless(auth()->user()->teams->contains($team->id), 403);

        try {
            return $team->downloadInvoice($invoiceId, [
                'vendor' => config('app.name'),
                'product' => config('electrik.name', 'Electrik'),
            ]);
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;

        return view('electrik::livewire.billing.invoices', [
            'team' => $team,
            'invoices' => $team?->hasStripeId() ? $team->invoices() : collect(),
        ]);
    }
}
