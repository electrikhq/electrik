<?php

namespace Electrik\Console;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Product as StripeProduct;
use Stripe\Price as StripePrice;

class SyncStripeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'electrik:stripe:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Stripe products and prices with the local database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Synchronizing Stripe data...');

        Stripe::setApiKey(config('services.stripe.secret'));

        if (!config('services.stripe.secret')) {
            $this->error('Stripe secret key is not configured. Please set STRIPE_SECRET in your .env file.');
            return 1;
        }

        // Sync Products
        $this->info('Synchronizing products...');
        $stripeProducts = StripeProduct::all(['active' => true]);
        
        $productModel = config('electrik.stripe_product_model', \App\Models\StripeProduct::class);
        
        foreach ($stripeProducts->autoPagingIterator() as $stripeProduct) {
            $productModel::updateOrCreate(
                ['stripe_product_id' => $stripeProduct->id],
                [
                    'name' => $stripeProduct->name,
                    'description' => $stripeProduct->description ?? '',
                ]
            );
            $this->line("  ✓ Synced product: {$stripeProduct->name}");
        }

        // Sync Prices (Plans)
        $this->info('Synchronizing prices (plans)...');
        $stripePrices = StripePrice::all(['active' => true]);
        
        $planModel = config('electrik.stripe_plan_model', \App\Models\StripePlan::class);
        
        foreach ($stripePrices->autoPagingIterator() as $stripePrice) {
            $product = $productModel::where('stripe_product_id', $stripePrice->product)->first();
            
            if (!$product) {
                $this->warn("  ⚠ Skipping price {$stripePrice->id} - product not found");
                continue;
            }

            $planModel::updateOrCreate(
                ['stripe_price_id' => $stripePrice->id],
                [
                    'stripe_product_id' => $product->id,
                    'name' => $stripePrice->nickname ?? $product->name,
                    'price' => $stripePrice->unit_amount,
                    'currency' => $stripePrice->currency,
                    'interval' => $stripePrice->recurring->interval ?? 'one_time',
                    'interval_count' => $stripePrice->recurring->interval_count ?? 1,
                ]
            );
            $planName = $stripePrice->nickname ?? $product->name;
            $this->line("  ✓ Synced plan: {$planName}");
        }

        $this->info('Stripe data synchronized successfully.');
        return 0;
    }
}

