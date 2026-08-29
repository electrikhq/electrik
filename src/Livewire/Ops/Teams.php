<?php

namespace Electrik\Livewire\Ops;

use Electrik\Concerns\AuthorizesOperatorAccess;
use Electrik\Models\Team;
use Electrik\Support\BillingStatus;
use Electrik\Support\UserModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('electrik::components.layouts.app')]
#[Title('Teams')]
class Teams extends Component
{
    use AuthorizesOperatorAccess;
    use WithPagination;

    #[Url]
    public string $search = '';

    public function mount(): void
    {
        $this->authorizeOperator();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $search = trim($this->search);

        $teams = Team::query()
            ->withCount('users')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15);

        $ownerNames = UserModel::query()
            ->whereIn('id', $teams->getCollection()->pluck('owner_id')->filter()->unique())
            ->pluck('name', 'id');

        $teams->setCollection(
            $teams->getCollection()->map(function (Team $team) use ($ownerNames) {
                $subscription = BillingStatus::subscriptionFor($team);
                $team->electrik_billing_label = BillingStatus::statusLabel($subscription);
                $team->electrik_past_due = BillingStatus::isPastDue($team);
                $team->electrik_owner_name = $ownerNames->get($team->owner_id);

                return $team;
            })
        );

        return view('electrik::livewire.ops.teams', [
            'teams' => $teams,
        ]);
    }
}
