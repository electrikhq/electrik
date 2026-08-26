<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Electrik
    |--------------------------------------------------------------------------
    */

    'name' => env('ELECTRIK_NAME', 'Electrik'),

    // Overwritten at boot from this package's composer.json (single source of truth).
    'version' => '5.x',

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'home' => env('ELECTRIK_HOME', '/dashboard'),

        'registration' => env('ELECTRIK_REGISTRATION', true),

        'email_verification' => env('ELECTRIK_EMAIL_VERIFICATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Onboarding
    |--------------------------------------------------------------------------
    */

    'onboarding' => [
        'enabled' => filter_var(env('ELECTRIK_ONBOARDING', true), FILTER_VALIDATE_BOOL),

        'exempt_routes' => [
            'onboarding',
            'billing.*',
            'teams.invitations.*',
            'verification.*',
            'logout',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Teams
    |--------------------------------------------------------------------------
    */

    'teams' => [
        'roles' => ['owner', 'admin', 'member'],

        'invite_expires_days' => (int) env('ELECTRIK_INVITE_EXPIRES_DAYS', 7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions (Spatie, team-scoped roles)
    |--------------------------------------------------------------------------
    */

    'permissions' => [
        'model' => \Electrik\Models\Permission::class,
        'role_model' => \Electrik\Models\Role::class,

        'catalog' => [
            'teams.view' => [
                'display_name' => 'View teams',
                'category' => 'Teams',
                'category_description' => 'Team workspace access',
            ],
            'teams.manage' => [
                'display_name' => 'Manage team settings',
                'category' => 'Teams',
                'category_description' => 'Team workspace access',
            ],
            'teams.invite' => [
                'display_name' => 'Invite members',
                'category' => 'Teams',
                'category_description' => 'Team workspace access',
            ],
            'teams.members' => [
                'display_name' => 'Manage members',
                'category' => 'Teams',
                'category_description' => 'Team workspace access',
            ],
            'billing.view' => [
                'display_name' => 'View billing',
                'category' => 'Billing',
                'category_description' => 'Subscription and invoices',
            ],
            'billing.manage' => [
                'display_name' => 'Manage billing',
                'category' => 'Billing',
                'category_description' => 'Subscription and invoices',
            ],
            'access.roles' => [
                'display_name' => 'Manage roles',
                'category' => 'Access',
                'category_description' => 'Roles and permissions',
            ],
        ],

        'role_permissions' => [
            'owner' => ['*'],
            'admin' => [
                'teams.view',
                'teams.manage',
                'teams.invite',
                'teams.members',
                'billing.view',
                'billing.manage',
                'access.roles',
            ],
            'member' => [
                'teams.view',
                'billing.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing (Cashier on Team)
    |--------------------------------------------------------------------------
    */

    'billing' => [
        'subscription_name' => env('ELECTRIK_DEFAULT_SUBSCRIPTION_NAME', 'electrik'),

        'cc_required_for_free_plan' => env('ELECTRIK_CC_REQUIRED_FOR_FREE_PLAN', false),

        'require_subscription' => filter_var(env('ELECTRIK_REQUIRE_SUBSCRIPTION', false), FILTER_VALIDATE_BOOL),

        'plan_features' => [
            'default' => [
                'custom_roles' => false,
                'max_members' => 5,
            ],
            'by_price_id' => [
                // 'price_xxx' => ['custom_roles' => true, 'max_members' => 50],
            ],
        ],

        'product_model' => \Electrik\Models\StripeProduct::class,

        'plan_model' => \Electrik\Models\StripePlan::class,
    ],

];
