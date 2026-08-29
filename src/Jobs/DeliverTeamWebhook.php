<?php

namespace Electrik\Jobs;

use Electrik\Models\TeamWebhook;
use Electrik\Models\TeamWebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DeliverTeamWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public int $webhookId,
        public string $event,
        public array $payload = [],
    ) {}

    public function handle(): void
    {
        $webhook = TeamWebhook::query()->find($this->webhookId);

        if (! $webhook || ! $webhook->enabled) {
            return;
        }

        $body = [
            'event' => $this->event,
            'team_id' => $webhook->team_id,
            'payload' => $this->payload,
            'sent_at' => now()->toIso8601String(),
        ];

        $json = json_encode($body);
        $signature = hash_hmac('sha256', (string) $json, (string) $webhook->secret);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Electrik-Event' => $this->event,
                    'X-Electrik-Signature' => $signature,
                ])
                ->withBody((string) $json, 'application/json')
                ->post($webhook->url);

            $successful = $response->successful();

            TeamWebhookDelivery::query()->create([
                'team_webhook_id' => $webhook->id,
                'event' => $this->event,
                'status_code' => $response->status(),
                'successful' => $successful,
                'response_body' => \Illuminate\Support\Str::limit($response->body(), 2000),
            ]);

            $webhook->forceFill([
                'last_triggered_at' => now(),
                'failure_count' => $successful ? 0 : $webhook->failure_count + 1,
            ])->save();
        } catch (\Throwable $e) {
            report($e);

            TeamWebhookDelivery::query()->create([
                'team_webhook_id' => $webhook->id,
                'event' => $this->event,
                'status_code' => null,
                'successful' => false,
                'response_body' => \Illuminate\Support\Str::limit($e->getMessage(), 2000),
            ]);

            $webhook->forceFill([
                'last_triggered_at' => now(),
                'failure_count' => $webhook->failure_count + 1,
            ])->save();
        }
    }
}
