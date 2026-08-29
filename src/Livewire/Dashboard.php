<?php

namespace Electrik\Livewire;

use Electrik\Concerns\ResolvesTeamBilling;
use Electrik\Models\Activity;
use Electrik\Models\Client;
use Electrik\Models\Project;
use Electrik\Models\Task;
use Electrik\Support\BillingStatus;
use Electrik\Support\SampleStudio;
use Illuminate\Support\Facades\Schema;
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
        $studio = [
            'clients' => 0,
            'projects_active' => 0,
            'tasks_open' => 0,
            'tasks_overdue' => 0,
        ];
        $recentActivity = collect();
        $recentProjects = collect();
        $myTasks = collect();
        $overdueTasks = collect();

        if ($team) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
            $canManageMembers = $user->can('teams.members') || $user->isOwnerOfTeam($team);

            if ($canManageMembers) {
                $pendingInvites = $team->invites()->count();
            }

            if (SampleStudio::enabled()) {
                $studio = [
                    'clients' => Client::query()->where('status', 'active')->count(),
                    'projects_active' => Project::query()->where('status', 'active')->count(),
                    'tasks_open' => Task::query()->where('status', '!=', 'done')->count(),
                    'tasks_overdue' => Task::query()
                        ->where('status', '!=', 'done')
                        ->whereNotNull('due_on')
                        ->whereDate('due_on', '<', now()->toDateString())
                        ->count(),
                ];
                $recentProjects = Project::query()
                    ->with('client')
                    ->withCount(['tasks as open_tasks_count' => fn ($q) => $q->where('status', '!=', 'done')])
                    ->latest()
                    ->limit(5)
                    ->get();
                $myTasks = Task::query()
                    ->with('project')
                    ->where('assignee_id', $user->id)
                    ->where('status', '!=', 'done')
                    ->orderBy('due_on')
                    ->limit(5)
                    ->get();
                $overdueTasks = Task::query()
                    ->with(['project', 'assignee'])
                    ->where('status', '!=', 'done')
                    ->whereNotNull('due_on')
                    ->whereDate('due_on', '<', now()->toDateString())
                    ->orderBy('due_on')
                    ->limit(5)
                    ->get();
            }

            if (Schema::hasTable(config('activitylog.table_name', 'activity_log'))) {
                $recentActivity = Activity::query()
                    ->where('team_id', $team->getKey())
                    ->latest()
                    ->limit(8)
                    ->get();
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
            'studio' => $studio,
            'recentProjects' => $recentProjects,
            'myTasks' => $myTasks,
            'overdueTasks' => $overdueTasks,
            'recentActivity' => $recentActivity,
            'studioEnabled' => SampleStudio::enabled(),
        ]);
    }
}
