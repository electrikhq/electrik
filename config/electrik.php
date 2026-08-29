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
    | Branding (white-label)
    |--------------------------------------------------------------------------
    */

    'branding' => [
        'logo_url' => env('ELECTRIK_LOGO_URL'),
        'logo_dark_url' => env('ELECTRIK_LOGO_DARK_URL'),
        'primary' => env('ELECTRIK_BRAND_PRIMARY'),
        'show_powered_by' => filter_var(env('ELECTRIK_SHOW_POWERED_BY', true), FILTER_VALIDATE_BOOL),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'home' => env('ELECTRIK_HOME', '/dashboard'),

        'registration' => env('ELECTRIK_REGISTRATION', true),

        'email_verification' => env('ELECTRIK_EMAIL_VERIFICATION', true),

        'socialite' => [
            'providers' => array_values(array_filter(array_map(
                'trim',
                explode(',', (string) env('ELECTRIK_SOCIALITE_PROVIDERS', 'google,github'))
            ))),
        ],

        'magic_link' => [
            'enabled' => filter_var(env('ELECTRIK_MAGIC_LINK', true), FILTER_VALIDATE_BOOL),
            'expire_minutes' => (int) env('ELECTRIK_MAGIC_LINK_EXPIRE', 15),
        ],

        'login_alerts' => filter_var(env('ELECTRIK_LOGIN_ALERTS', true), FILTER_VALIDATE_BOOL),
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
            'users.impersonate' => [
                'display_name' => 'Impersonate members',
                'category' => 'Access',
                'category_description' => 'Roles and permissions',
            ],
            'tokens.manage' => [
                'display_name' => 'Manage team API tokens',
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
                'users.impersonate',
                'tokens.manage',
            ],
            'member' => [
                'teams.view',
                'billing.view',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API tokens (Sanctum)
    |--------------------------------------------------------------------------
    */

    'api_tokens' => [
        /*
         * Default expiry preset when creating a token: never|7|30|90|365
         */
        'default_expiration_days' => env('ELECTRIK_API_TOKEN_EXPIRATION_DAYS', '90'),

        /*
         * Ability catalog shown when creating tokens. Values are Sanctum abilities.
         *
         * @var array<string, string> ability => label
         */
        'abilities' => [
            '*' => 'Full access',
            'read' => 'Read',
            'write' => 'Write',
            'billing:read' => 'Billing (read)',
            'billing:write' => 'Billing (write)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Operators (app-level /ops)
    |--------------------------------------------------------------------------
    */

    'operators' => [
        /*
         * Comma-separated emails in ELECTRIK_OPERATOR_EMAILS, or list in config.
         */
        'emails' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('ELECTRIK_OPERATOR_EMAILS', ''))
        ))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | User Profile language options. Guests always use APP_LOCALE.
    | Supported: en, es, fr, ar (RTL), hi
    |
    */

    'locales' => ['en', 'es', 'fr', 'ar', 'hi'],

    /*
    |--------------------------------------------------------------------------
    | Billing (Cashier on Team)
    |--------------------------------------------------------------------------
    */

    'billing' => [
        /*
         * Billing processor. Only stripe (laravel/cashier) is supported.
         */
        'driver' => env('ELECTRIK_BILLING_DRIVER', 'stripe'),

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

    /*
    |--------------------------------------------------------------------------
    | Sample Studio micro-app
    |--------------------------------------------------------------------------
    |
    | Ships a team-scoped Clients → Projects → Tasks demo so Electrik feels like
    | a real product shell. Disable once you replace it with your own models.
    |
    */

    'sample' => [
        'projects' => filter_var(env('ELECTRIK_SAMPLE_PROJECTS', true), FILTER_VALIDATE_BOOL),
    ],

];
