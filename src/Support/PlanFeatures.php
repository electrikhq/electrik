<?php

namespace Electrik\Support;

use Electrik\Models\StripePlan;
use Electrik\Models\Team;

class PlanFeatures
{
    /**
     * @return array<string, mixed>
     */
    public static function forTeam(?Team $team): array
    {
        $defaults = config('electrik.billing.plan_features.default', []);

        if (! $team) {
            return $defaults;
        }

        $subscription = BillingStatus::subscriptionFor($team);
        $plan = null;

        if ($subscription) {
            $priceId = $subscription->stripe_price
                ?? $subscription->items()->first()?->stripe_price;
            $planModel = config('electrik.billing.plan_model', StripePlan::class);
            $plan = $priceId
                ? $planModel::query()->where('stripe_price_id', $priceId)->first()
                : null;
        }

        if (! $plan) {
            return $defaults;
        }

        $fromPlan = is_array($plan->features) ? $plan->features : [];
        $fromConfig = config('electrik.billing.plan_features.by_price_id.'.$plan->stripe_price_id, []);

        return array_merge($defaults, $fromConfig, $fromPlan);
    }

    public static function has(?Team $team, string $feature): bool
    {
        $features = static::forTeam($team);

        return (bool) ($features[$feature] ?? false);
    }

    public static function limit(?Team $team, string $key): ?int
    {
        $features = static::forTeam($team);
        $value = $features[$key] ?? null;

        return is_numeric($value) ? (int) $value : null;
    }

    public static function canAddMember(Team $team): bool
    {
        $remaining = static::memberSlotsRemaining($team);

        return $remaining === null || $remaining > 0;
    }

    public static function memberSlotsRemaining(Team $team): ?int
    {
        $max = static::limit($team, 'max_members');

        if ($max === null) {
            return null;
        }

        $used = $team->users()->count() + $team->invites()->count();

        return max(0, $max - $used);
    }

    public static function canInviteMember(Team $team): bool
    {
        return static::canAddMember($team);
    }

    public static function planForTeam(?Team $team): ?StripePlan
    {
        return BillingStatus::planFor($team);
    }
}
