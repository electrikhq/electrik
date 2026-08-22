<?php

namespace Electrik\Actions\Billing;

use Electrik\Models\Team;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription;
use Stripe\Checkout\Session;

class SyncCheckoutSubscription
{
    /**
     * Persist a Cashier subscription from a completed Stripe Checkout session.
     * Used when webhooks are delayed or missed (common in local stripe listen).
     */
    public function execute(Team $team, string $checkoutSessionId): ?Subscription
    {
        $session = Cashier::stripe()->checkout->sessions->retrieve($checkoutSessionId, [
            'expand' => ['subscription'],
        ]);

        if ($session->status !== 'complete' || $session->mode !== 'subscription') {
            return null;
        }

        if ($session->customer && $team->stripe_id && $session->customer !== $team->stripe_id) {
            return null;
        }

        $stripeSubscription = $session->subscription;

        if (is_string($stripeSubscription)) {
            $stripeSubscription = Cashier::stripe()->subscriptions->retrieve($stripeSubscription);
        }

        if (! $stripeSubscription) {
            return null;
        }

        return $this->syncStripeSubscription($team, $stripeSubscription);
    }

    /**
     * Sync a Stripe subscription object onto the team (Cashier webhook shape).
     */
    public function syncStripeSubscription(Team $team, object $data): Subscription
    {
        $payload = is_array($data) ? $data : $data->toArray();

        if (isset($payload['trial_end']) && $payload['trial_end']) {
            $trialEndsAt = Carbon::createFromTimestamp($payload['trial_end']);
        } else {
            $trialEndsAt = null;
        }

        $items = $payload['items']['data'] ?? [];
        $firstItem = $items[0] ?? null;
        $isSinglePrice = count($items) === 1;

        $type = $payload['metadata']['type']
            ?? $payload['metadata']['name']
            ?? config('electrik.billing.subscription_name', 'electrik');

        $subscription = $team->subscriptions()->updateOrCreate([
            'stripe_id' => $payload['id'],
        ], [
            'type' => $type,
            'stripe_status' => $payload['status'],
            'stripe_price' => $isSinglePrice ? ($firstItem['price']['id'] ?? null) : null,
            'quantity' => $isSinglePrice && isset($firstItem['quantity']) ? $firstItem['quantity'] : null,
            'trial_ends_at' => $trialEndsAt,
            'ends_at' => null,
        ]);

        foreach ($items as $item) {
            $subscription->items()->updateOrCreate([
                'stripe_id' => $item['id'],
            ], [
                'stripe_product' => $item['price']['product'],
                'stripe_price' => $item['price']['id'],
                'quantity' => $item['quantity'] ?? null,
            ]);
        }

        return $subscription->fresh(['items']);
    }
}
