<?php

namespace Electrik\Support;

use Electrik\Models\StripePlan;
use Electrik\Models\Team;
use Illuminate\Http\Request;
use Laravel\Cashier\Subscription;

class BillingStatus
{
    public static function subscriptionFor(?Team $team): ?Subscription
    {
        if (! $team) {
            return null;
        }

        return $team->subscription(config('electrik.billing.subscription_name', 'electrik'));
    }

    public static function teamHasAccess(?Team $team): bool
    {
        if (! $team) {
            return false;
        }

        if (! config('electrik.billing.require_subscription', false)) {
            return true;
        }

        $subscription = static::subscriptionFor($team);

        if ($subscription && ($subscription->active() || $subscription->onTrial() || $subscription->onGracePeriod())) {
            return true;
        }

        // past_due still has a subscription but payment failed — deny gated features.
        if ($subscription && in_array($subscription->stripe_status, ['past_due', 'unpaid'], true)) {
            return false;
        }

        $planModel = config('electrik.billing.plan_model', StripePlan::class);

        return $planModel::query()->where('price', 0)->exists();
    }

    public static function isPastDue(?Team $team): bool
    {
        $subscription = static::subscriptionFor($team);

        return $subscription && in_array($subscription->stripe_status, ['past_due', 'unpaid'], true);
    }

    public static function subscriptionRequired(): bool
    {
        return (bool) config('electrik.billing.require_subscription', false);
    }

    public static function shouldShowBanner(?Team $team, ?Request $request = null): bool
    {
        if (! $team) {
            return false;
        }

        $request ??= request();

        if (Onboarding::needs(auth()->user())) {
            return false;
        }

        if ($request->routeIs('billing.*', 'onboarding')) {
            return false;
        }

        if (static::isPastDue($team)) {
            return true;
        }

        if (! static::subscriptionRequired()) {
            return false;
        }

        return ! static::teamHasAccess($team);
    }

    public static function bannerMessage(?Team $team): string
    {
        if (static::isPastDue($team)) {
            return __('Your last payment failed. Update your payment method to keep access.');
        }

        return __('You are not subscribed to a plan. Choose a plan to unlock the app.');
    }

    public static function bannerCta(?Team $team): array
    {
        if (static::isPastDue($team)) {
            return [
                'label' => __('Update payment method'),
                'route' => 'billing.payment-methods',
            ];
        }

        return [
            'label' => __('View plans'),
            'route' => 'billing.plans',
        ];
    }

    public static function planFor(?Team $team): ?StripePlan
    {
        $subscription = static::subscriptionFor($team);

        if (! $subscription) {
            return null;
        }

        $priceId = $subscription->stripe_price
            ?? $subscription->items()->first()?->stripe_price;

        if (! $priceId) {
            return null;
        }

        $planModel = config('electrik.billing.plan_model', StripePlan::class);

        return $planModel::query()->where('stripe_price_id', $priceId)->first();
    }

    public static function trialLabel(?Subscription $subscription): ?string
    {
        if (! $subscription?->onTrial()) {
            return null;
        }

        $endsAt = $subscription->trial_ends_at;

        if (! $endsAt) {
            return __('Trial active');
        }

        $days = (int) now()->diffInDays($endsAt, false);

        if ($days < 0) {
            return null;
        }

        if ($days === 0) {
            return __('Trial ends today');
        }

        return trans_choice('{1} :count day left on trial|[2,*] :count days left on trial', $days, [
            'count' => $days,
        ]);
    }

    public static function statusLabel(?Subscription $subscription): string
    {
        if (! $subscription) {
            return __('No plan');
        }

        if ($subscription->onTrial()) {
            return __('Trial');
        }

        if ($subscription->onGracePeriod()) {
            return __('Grace period');
        }

        if ($subscription->active()) {
            return __('Active');
        }

        return ucfirst(str_replace('_', ' ', (string) $subscription->stripe_status));
    }

    /**
     * @return list<array{key: string, label: string, ok: bool, hint: string|null}>
     */
    public static function healthChecks(): array
    {
        $webhookUrl = url('/stripe/webhook');

        return [
            [
                'key' => 'stripe_key',
                'label' => 'Stripe publishable key',
                'ok' => filled(config('cashier.key') ?: env('STRIPE_KEY')),
                'hint' => 'Set STRIPE_KEY in .env',
            ],
            [
                'key' => 'stripe_secret',
                'label' => 'Stripe secret key',
                'ok' => filled(config('cashier.secret') ?: env('STRIPE_SECRET')),
                'hint' => 'Set STRIPE_SECRET in .env',
            ],
            [
                'key' => 'webhook_secret',
                'label' => 'Stripe webhook secret',
                'ok' => filled(config('cashier.webhook.secret') ?: env('STRIPE_WEBHOOK_SECRET')),
                'hint' => 'Set STRIPE_WEBHOOK_SECRET (stripe listen --print-secret locally)',
            ],
            [
                'key' => 'webhook_url',
                'label' => 'Webhook endpoint',
                'ok' => true,
                'hint' => $webhookUrl,
            ],
        ];
    }

    public static function healthOk(): bool
    {
        foreach (static::healthChecks() as $check) {
            if ($check['key'] === 'webhook_url') {
                continue;
            }

            if (! $check['ok']) {
                return false;
            }
        }

        return true;
    }
}
