<?php

namespace Electrik\Support;

use Electrik\Jobs\DeliverTeamWebhook;
use Electrik\Models\Team;
use Electrik\Models\TeamWebhook;

class TeamWebhookDispatcher
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public static function dispatch(Team $team, string $event, array $payload = []): void
    {
        if (! class_exists(TeamWebhook::class)) {
            return;
        }

        $team->webhooks()
            ->where('enabled', true)
            ->get()
            ->filter(fn (TeamWebhook $webhook) => $webhook->listensFor($event))
            ->each(function (TeamWebhook $webhook) use ($event, $payload): void {
                DeliverTeamWebhook::dispatch($webhook->id, $event, $payload);
            });
    }
}
