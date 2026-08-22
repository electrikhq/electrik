<?php

namespace Electrik\Console;

use Electrik\Models\StripePlan;
use Electrik\Models\StripeProduct;
use Illuminate\Console\Command;
use Stripe\Price as StripePriceApi;
use Stripe\Product as StripeProductApi;
use Stripe\Stripe;

class SyncStripeCommand extends Command
{
    protected $signature = 'electrik:stripe:sync';

    protected $description = 'Sync Stripe products and prices into the local catalog';

    public function handle(): int
    {
        $secret = config('cashier.secret');

        if (! $secret) {
            $this->components->error('STRIPE_SECRET is not set.');

            return self::FAILURE;
        }

        Stripe::setApiKey($secret);

        $this->components->info('Syncing Stripe products…');

        $productModel = config('electrik.billing.product_model', StripeProduct::class);
        $planModel = config('electrik.billing.plan_model', StripePlan::class);

        foreach (StripeProductApi::all(['active' => true])->autoPagingIterator() as $stripeProduct) {
            $productModel::updateOrCreate(
                ['stripe_product_id' => $stripeProduct->id],
                [
                    'name' => $stripeProduct->name,
                    'description' => $stripeProduct->description ?? '',
                ]
            );
            $this->line("  · {$stripeProduct->name}");
        }

        $this->components->info('Syncing Stripe prices…');

        foreach (StripePriceApi::all(['active' => true])->autoPagingIterator() as $stripePrice) {
            $product = $productModel::query()
                ->where('stripe_product_id', $stripePrice->product)
                ->first();

            if (! $product) {
                $this->components->warn("Skip {$stripePrice->id}: product missing locally");

                continue;
            }

            $planModel::updateOrCreate(
                ['stripe_price_id' => $stripePrice->id],
                [
                    'stripe_product_id' => $product->id,
                    'name' => $stripePrice->nickname ?: $product->name,
                    'price' => $stripePrice->unit_amount ?? 0,
                    'currency' => $stripePrice->currency,
                    'interval' => $stripePrice->recurring->interval ?? 'one_time',
                    'interval_count' => $stripePrice->recurring->interval_count ?? 1,
                ]
            );

            $this->line('  · '.($stripePrice->nickname ?: $product->name));
        }

        $this->components->info('Stripe catalog synced.');

        return self::SUCCESS;
    }
}
