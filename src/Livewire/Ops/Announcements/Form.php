<?php

namespace Electrik\Livewire\Ops\Announcements;

use Electrik\Concerns\AuthorizesOperatorAccess;
use Electrik\Models\Announcement;
use Electrik\Models\StripePlan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('electrik::components.layouts.app')]
#[Title('Announcement')]
class Form extends Component
{
    use AuthorizesOperatorAccess;

    public ?Announcement $announcement = null;

    public string $title = '';

    public string $body = '';

    public string $audience = 'all';

    public ?string $plan_price_id = null;

    public ?string $starts_at = null;

    public ?string $ends_at = null;

    public function mount(?Announcement $announcement = null): void
    {
        $this->authorizeOperator();

        if (! $announcement) {
            return;
        }

        $this->announcement = $announcement;
        $this->title = $announcement->title;
        $this->body = $announcement->body;
        $this->audience = $announcement->audience;
        $this->plan_price_id = $announcement->plan_price_id;
        $this->starts_at = $announcement->starts_at?->format('Y-m-d\TH:i');
        $this->ends_at = $announcement->ends_at?->format('Y-m-d\TH:i');
    }

    public function save(): void
    {
        $this->authorizeOperator();

        $this->starts_at = $this->starts_at ?: null;
        $this->ends_at = $this->ends_at ?: null;
        $this->plan_price_id = $this->plan_price_id ?: null;

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
            'audience' => ['required', 'in:all,operators,plan'],
            'plan_price_id' => ['nullable', 'required_if:audience,plan', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        if ($validated['audience'] !== 'plan') {
            $validated['plan_price_id'] = null;
        }

        if ($this->announcement) {
            $this->announcement->update($validated);
            $message = __('Announcement updated.');
        } else {
            $validated['created_by'] = auth()->id();
            $this->announcement = Announcement::create($validated);
            $message = __('Announcement created.');
        }

        session()->flash('status', $message);

        $this->redirect(route('ops.announcements.index'), navigate: true);
    }

    public function render()
    {
        $planModel = config('electrik.billing.plan_model', StripePlan::class);

        return view('electrik::livewire.ops.announcements.form', [
            'editing' => (bool) $this->announcement,
            'plans' => $planModel::query()->orderBy('price')->get(),
        ]);
    }
}
