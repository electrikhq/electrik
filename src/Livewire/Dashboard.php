<?php

namespace Electrik\Livewire;

use Electrik\Concerns\ResolvesTeamBilling;
use Electrik\Support\BillingStatus;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('electrik::components.layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    use ResolvesTeamBilling;

    public function render()
    {
        $user = auth()->user();
        $team = $user?->currentTeam;
        $subscription = $this->teamSubscription($team);
        $plan = $this->planForSubscription($subscription);

        $memberCount = $team ? $team->users()->count() : 0;
        $pendingInvites = 0;
        $canManageMembers = false;

        if ($team) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
            $canManageMembers = $user->can('teams.members') || $user->isOwnerOfTeam($team);

            if ($canManageMembers) {
                $pendingInvites = $team->invites()->count();
            }
        }

        return view('electrik::livewire.dashboard', [
            'user' => $user,
            'team' => $team,
            'subscription' => $subscription,
            'plan' => $plan,
            'memberCount' => $memberCount,
            'pendingInvites' => $pendingInvites,
            'canManageMembers' => $canManageMembers,
            'trialLabel' => BillingStatus::trialLabel($subscription),
            'statusLabel' => BillingStatus::statusLabel($subscription),
            'needsSubscription' => $team && BillingStatus::subscriptionRequired() && ! BillingStatus::teamHasAccess($team),
        ]);
    }
}
