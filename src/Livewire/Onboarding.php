<?php

namespace Electrik\Livewire;

use Electrik\Actions\Billing\CreateSubscription;
use Electrik\Concerns\AuthorizesTeamAccess;
use Electrik\Models\StripePlan;
use Electrik\Notifications\TeamInvitationNotification;
use Electrik\Support\Onboarding as UserOnboarding;
use Electrik\Support\PlanFeatures;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mpociot\Teamwork\Facades\Teamwork;

#[Layout('electrik::components.layouts.onboarding')]
#[Title('Get started')]
class Onboarding extends Component
{
    use AuthorizesTeamAccess;

    public int $step = 1;

    public string $teamName = '';

    public string $inviteEmail = '';

    public function mount(): void
    {
        if (! UserOnboarding::needs(auth()->user())) {
            $this->redirect(config('electrik.auth.home', '/dashboard'), navigate: true);

            return;
        }

        $team = auth()->user()?->currentTeam;
        $this->teamName = $team?->name ?? trim((string) auth()->user()?->name)."'s Team";

        if (request()->query('checkout') === 'success' && $team) {
            $sessionId = (string) request()->query('session_id', '');

            if ($sessionId !== '') {
                try {
                    app(\Electrik\Actions\Billing\SyncCheckoutSubscription::class)->execute($team, $sessionId);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $this->step = 3;
        }
    }

    public function saveTeam(): void
    {
        $team = auth()->user()?->currentTeam;

        abort_unless($team, 422);

        $this->bindTeamContext($team);

        $validated = $this->validate([
            'teamName' => ['required', 'string', 'max:255'],
        ]);

        $team->update(['name' => $validated['teamName']]);

        $planModel = config('electrik.billing.plan_model', StripePlan::class);
        $this->step = $planModel::query()->exists() ? 2 : 3;
    }

    public function subscribe(int $planId, CreateSubscription $createSubscription): mixed
    {
        $team = auth()->user()?->currentTeam;
        abort_unless($team, 422);

        $this->bindTeamContext($team);
        abort_unless(auth()->user()->can('billing.manage') || auth()->user()->isOwnerOfTeam($team), 403);

        $planModel = config('electrik.billing.plan_model', StripePlan::class);
        $plan = $planModel::query()->findOrFail($planId);

        try {
            if ($plan->isFree() && ! config('electrik.billing.cc_required_for_free_plan', false)) {
                $createSubscription->execute($team, $plan);
                session()->flash('status', __('Subscribed to :plan.', ['plan' => $plan->name]));
                $this->step = 3;

                return null;
            }

            $name = config('electrik.billing.subscription_name', 'electrik');
            $checkout = $team->newSubscription($name, $plan->stripe_price_id)->checkout([
                'success_url' => route('onboarding', absolute: true).'?checkout=success&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('onboarding', absolute: true).'?checkout=cancelled',
            ]);

            return $this->redirect($checkout->url);
        } catch (\Throwable $e) {
            $this->addError('plan', $e->getMessage());

            return null;
        }
    }

    public function skipPlan(): void
    {
        $this->step = 3;
    }

    public function sendInvite(): void
    {
        $team = auth()->user()?->currentTeam;
        abort_unless($team, 422);

        $this->authorizeTeamPermission($team, 'teams.invite');

        if ($this->inviteEmail === '') {
            $this->finish();

            return;
        }

        $validated = $this->validate([
            'inviteEmail' => ['required', 'email', 'max:255'],
        ]);

        if (! PlanFeatures::canInviteMember($team)) {
            $this->addError('inviteEmail', __('Your plan member limit has been reached.'));

            return;
        }

        if (strcasecmp($validated['inviteEmail'], (string) auth()->user()->email) === 0) {
            $this->addError('inviteEmail', __('You cannot invite yourself.'));

            return;
        }

        if (Teamwork::hasPendingInvite($validated['inviteEmail'], $team)) {
            $this->addError('inviteEmail', __('This email already has a pending invitation.'));

            return;
        }

        if ($team->users()->where('email', $validated['inviteEmail'])->exists()) {
            $this->addError('inviteEmail', __('This person is already on the team.'));

            return;
        }

        $days = max(1, (int) config('electrik.teams.invite_expires_days', 7));
        $assignable = $this->assignableRoleNamesFor($team);
        $role = $assignable[0] ?? 'member';

        Teamwork::inviteToTeam($validated['inviteEmail'], $team, function ($invite) use ($role, $days) {
            if (Schema::hasColumn($invite->getTable(), 'role')) {
                $invite->role = $role;
            }
            if (Schema::hasColumn($invite->getTable(), 'expires_at')) {
                $invite->expires_at = now()->addDays($days);
            }
            $invite->save();

            Notification::route('mail', $invite->email)
                ->notify(new TeamInvitationNotification($invite));
        });

        session()->flash('status', __('Invitation sent.'));
        $this->finish();
    }

    public function skipInvite(): void
    {
        $this->finish();
    }

    public function finish(): void
    {
        UserOnboarding::markCompleted(auth()->user());

        $this->redirect(config('electrik.auth.home', '/dashboard'), navigate: true);
    }

    public function render()
    {
        $team = auth()->user()?->currentTeam;
        $planModel = config('electrik.billing.plan_model', StripePlan::class);

        $plans = $planModel::query()
            ->with('product')
            ->orderBy('price')
            ->get();

        $showPlanStep = $plans->isNotEmpty();

        return view('electrik::livewire.onboarding', [
            'team' => $team,
            'plans' => $plans,
            'showPlanStep' => $showPlanStep,
            'totalSteps' => $showPlanStep ? 3 : 2,
        ]);
    }
}
