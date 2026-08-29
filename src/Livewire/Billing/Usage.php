<?php

namespace Electrik\Livewire\Billing;

use Electrik\Concerns\ResolvesTeamBilling;
use Electrik\Models\StripePlan;
use Electrik\Support\Billing\ReportUsage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Usage')]
class Usage extends Component
{
    use ResolvesTeamBilling;

    public int $quantity = 1;

    public ?int $planId = null;

    public function mount(): void
    {
        $this->currentTeamOrRedirect();
        abort_unless(
            auth()->user()->can('billing.view') || auth()->user()->isOwnerOfTeam(auth()->user()->currentTeam),
            403
        );
    }

    public function report(ReportUsage $reportUsage): void
    {
        $team = $this->currentTeamOrRedirect();

        if (! $team) {
            return;
        }

        abort_unless(
            auth()->user()->can('billing.manage') || auth()->user()->isOwnerOfTeam($team),
            403
        );

        $validated = $this->validate([
            'planId' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        $plan = StripePlan::query()->findOrFail($validated['planId']);

        try {
            $reportUsage->executeForPlan($team, $plan, $validated['quantity']);
            session()->flash('status', __('Reported :qty units of usage.', ['qty' => $validated['quantity']]));
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $metered = StripePlan::query()
            ->where('metered', true)
            ->orderBy('name')
            ->get();

        return view('electrik::livewire.billing.usage', [
            'meteredPlans' => $metered,
        ]);
    }
}
