<?php

namespace Electrik\Listeners;

use Electrik\Models\StripeWebhookEvent;
use Electrik\Models\Team;
use Illuminate\Support\Facades\Schema;
use Laravel\Cashier\Events\WebhookHandled;
use Laravel\Cashier\Events\WebhookReceived;

class LogStripeWebhook
{
    public function handleReceived(WebhookReceived $event): void
    {
        $this->persist($event->payload, 'received');
    }

    public function handleHandled(WebhookHandled $event): void
    {
        $this->persist($event->payload, 'handled');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function persist(array $payload, string $status): void
    {
        if (! Schema::hasTable('stripe_webhook_events')) {
            return;
        }

        $type = (string) ($payload['type'] ?? 'unknown');
        $stripeId = isset($payload['id']) ? (string) $payload['id'] : null;
        $object = is_array($payload['data']['object'] ?? null) ? $payload['data']['object'] : [];
        $customerId = isset($object['customer']) ? (string) $object['customer'] : null;

        $teamId = null;
        if ($customerId) {
            $teamId = Team::query()->where('stripe_id', $customerId)->value('id');
        }

        $summary = [
            'object' => $object['object'] ?? null,
            'id' => $object['id'] ?? null,
            'status' => $object['status'] ?? null,
        ];

        if ($stripeId) {
            $existing = StripeWebhookEvent::query()->where('stripe_id', $stripeId)->first();

            if ($existing) {
                $existing->fill([
                    'status' => $status,
                    'processed_at' => $status === 'handled' ? now() : $existing->processed_at,
                    'team_id' => $teamId ?: $existing->team_id,
                    'customer_id' => $customerId ?: $existing->customer_id,
                    'payload_summary' => $summary,
                ])->save();

                return;
            }
        }

        StripeWebhookEvent::query()->create([
            'stripe_id' => $stripeId,
            'type' => $type,
            'status' => $status,
            'customer_id' => $customerId,
            'team_id' => $teamId,
            'payload_summary' => $summary,
            'processed_at' => $status === 'handled' ? now() : null,
        ]);
    }
}
