<?php

namespace Electrik\Livewire\Ops;

use Electrik\Concerns\AuthorizesOperatorAccess;
use Electrik\Models\StripePlan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Plan features')]
class Plans extends Component
{
    use AuthorizesOperatorAccess;

    public ?int $editingId = null;

    public string $featuresJson = '{}';

    public bool $isAddon = false;

    public bool $metered = false;

    public function mount(): void
    {
        $this->authorizeOperator();
    }

    public function edit(int $planId): void
    {
        $plan = StripePlan::query()->findOrFail($planId);
        $this->editingId = $plan->id;
        $this->featuresJson = json_encode($plan->features ?? new \stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->isAddon = (bool) $plan->is_addon;
        $this->metered = (bool) $plan->metered;
    }

    public function save(): void
    {
        $this->authorizeOperator();

        $validated = $this->validate([
            'editingId' => ['required', 'integer'],
            'featuresJson' => ['required', 'string'],
            'isAddon' => ['boolean'],
            'metered' => ['boolean'],
        ]);

        $decoded = json_decode($validated['featuresJson'], true);

        if (! is_array($decoded)) {
            $this->addError('featuresJson', __('Features must be a JSON object.'));

            return;
        }

        $plan = StripePlan::query()->findOrFail($validated['editingId']);
        $plan->update([
            'features' => $decoded,
            'is_addon' => $this->isAddon,
            'metered' => $this->metered,
        ]);

        session()->flash('status', __('Plan updated.'));
        $this->reset('editingId', 'featuresJson', 'isAddon', 'metered');
        $this->featuresJson = '{}';
    }

    public function render()
    {
        return view('electrik::livewire.ops.plans', [
            'plans' => StripePlan::query()->with('product')->orderBy('name')->get(),
        ]);
    }
}
