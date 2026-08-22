<?php

namespace Electrik\Console;

use Electrik\Actions\Billing\SyncCheckoutSubscription;
use Electrik\Models\Team;
use Illuminate\Console\Command;
use Laravel\Cashier\Cashier;

class SyncSubscriptionsCommand extends Command
{
    protected $signature = 'electrik:stripe:sync-subscriptions {--team= : Team ID to sync (default: all teams with a Stripe customer)}';

    protected $description = 'Pull Stripe subscriptions into the local Cashier tables (repair missed webhooks)';

    public function handle(SyncCheckoutSubscription $sync): int
    {
        $query = Team::query()->whereNotNull('stripe_id');

        if ($teamId = $this->option('team')) {
            $query->whereKey($teamId);
        }

        $teams = $query->get();

        if ($teams->isEmpty()) {
            $this->components->warn('No teams with a Stripe customer found.');

            return self::SUCCESS;
        }

        foreach ($teams as $team) {
            $this->components->info("Team #{$team->id} {$team->name} ({$team->stripe_id})");

            $stripeSubs = Cashier::stripe()->subscriptions->all([
                'customer' => $team->stripe_id,
                'status' => 'all',
                'limit' => 100,
            ]);

            if (count($stripeSubs->data) === 0) {
                $this->line('  · no Stripe subscriptions');

                continue;
            }

            foreach ($stripeSubs->data as $stripeSub) {
                $local = $sync->syncStripeSubscription($team, $stripeSub);
                $this->line("  · {$local->stripe_id} → {$local->stripe_status} ({$local->type})");
            }
        }

        $this->components->info('Done.');

        return self::SUCCESS;
    }
}
