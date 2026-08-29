<?php

namespace Electrik\Livewire\Ops;

use Electrik\Concerns\AuthorizesOperatorAccess;
use Electrik\Models\StripeWebhookEvent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('electrik::components.layouts.app')]
#[Title('Webhooks')]
class Webhooks extends Component
{
    use AuthorizesOperatorAccess;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeOperator();
    }

    public function render()
    {
        return view('electrik::livewire.ops.webhooks', [
            'events' => StripeWebhookEvent::query()
                ->with('team')
                ->latest('id')
                ->paginate(20),
        ]);
    }
}
