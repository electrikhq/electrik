<?php

return [

    /**
     * If you want to collect credit cards for free plans, set this value to true.
     * If set false, it will not ask for credit card and billing details for free plans.
     */
    'cc_required_for_free_plan' => env('ELECTRIK_CC_REQUIRED_FOR_FREE_PLAN', false),

    /**
     * This is the default subscription name which will be saved in the db.
     * This is for internal use only and will not be shown to users.
     */
    'default_subscription_name' => env('ELECTRIK_DEFAULT_SUBSCRIPTION_NAME', 'electrik'),

    /**
     * Model class for Stripe products.
     * Products are synced from Stripe and stored in the database.
     */
    'stripe_product_model' => env('ELECTRIK_STRIPE_PRODUCT_MODEL', \App\Models\StripeProduct::class),

    /**
     * Model class for Stripe plans (prices).
     * Plans are synced from Stripe prices and stored in the database.
     */
    'stripe_plan_model' => env('ELECTRIK_STRIPE_PLAN_MODEL', \App\Models\StripePlan::class),

    /**
     * Routes that should not show the secondary sidebar.
     * These routes will only show the primary sidebar (if authenticated).
     */
    'routes_without_sidebar' => [
        'dashboard.index',
    ],

];

