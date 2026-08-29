<?php

namespace Electrik\Http\Middleware;

use Closure;
use Electrik\Models\Team;
use Electrik\Notifications\DatabaseNotification;
use Electrik\Support\BillingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $team = $user?->currentTeam;

        if ($team instanceof Team) {
            $this->notifyBillingManagersOncePerDay($team);
        }

        if (! BillingStatus::subscriptionRequired()) {
            return $next($request);
        }

        if (! $user || ! $team) {
            return redirect()->route('teams.index');
        }

        if (BillingStatus::teamHasAccess($team)) {
            return $next($request);
        }

        return redirect()
            ->route('billing.plans')
            ->with('error', __('Please choose a subscription plan to continue.'));
    }

    /**
     * Notify billing.manage users (and the owner) once per team per day when
     * payment has failed. Cheap cache guard keeps this from spamming on every request.
     */
    protected function notifyBillingManagersOncePerDay(Team $team): void
    {
        if (! BillingStatus::isPastDue($team)) {
            return;
        }

        $cacheKey = 'electrik.pastdue-notified.'.$team->getKey().'.'.now()->format('Y-m-d');

        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, now()->endOfDay());

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->getKey());

        $recipients = $team->users()
            ->get()
            ->filter(fn ($member) => (method_exists($member, 'can') && $member->can('billing.manage'))
                || (method_exists($member, 'isOwnerOfTeam') && $member->isOwnerOfTeam($team)));

        foreach ($recipients as $recipient) {
            DatabaseNotification::send(
                $recipient,
                __('Payment failed'),
                __('Your last payment for :team failed. Update your payment method to keep access.', ['team' => $team->name]),
                route('billing.payment-methods')
            );
        }
    }
}
