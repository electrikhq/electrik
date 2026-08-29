<?php

namespace Electrik;

use Electrik\Console\InstallCommand;
use Electrik\Console\MakeLivewireCommand;
use Electrik\Console\MakeModelCommand;
use Electrik\Console\MakeResourceCommand;
use Electrik\Console\ResetOnboardingCommand;
use Electrik\Console\SeedDemoCommand;
use Electrik\Console\SkipOnboardingForExistingCommand;
use Electrik\Console\SyncPermissionsCommand;
use Electrik\Console\SyncStripeCommand;
use Electrik\Console\SyncSubscriptionsCommand;
use Electrik\Http\Middleware\BindTeamFromAccessToken;
use Electrik\Http\Middleware\EnsureOperator;
use Electrik\Http\Middleware\EnsurePlanFeature;
use Electrik\Http\Middleware\EnsureTeamIpAllowed;
use Electrik\Http\Middleware\EnsureTokenAbility;
use Electrik\Http\Middleware\SetLocale;
use Electrik\Listeners\AssignTeamRoleOnJoin;
use Electrik\Listeners\CreateDefaultTeam;
use Electrik\Listeners\LogImpersonationActivity;
use Electrik\Listeners\LogStripeWebhook;
use Electrik\Listeners\SendNewLoginAlert;
use Electrik\Livewire\Auth\ForgotPassword;
use Electrik\Livewire\Auth\Login;
use Electrik\Livewire\Auth\Register;
use Electrik\Livewire\Auth\ResetPassword;
use Electrik\Livewire\Auth\VerifyEmail;
use Electrik\Livewire\Billing\Address as BillingAddress;
use Electrik\Livewire\Billing\Index as BillingIndex;
use Electrik\Livewire\Billing\Invoices as BillingInvoices;
use Electrik\Livewire\Billing\PaymentMethods as BillingPaymentMethods;
use Electrik\Livewire\Billing\Plans as BillingPlans;
use Electrik\Livewire\Billing\Subscription as BillingSubscription;
use Electrik\Livewire\Billing\Usage as BillingUsage;
use Electrik\Livewire\Dashboard;
use Electrik\Livewire\NotificationBell;
use Electrik\Livewire\Onboarding;
use Electrik\Livewire\Ops\Announcements\Form as OpsAnnouncementsForm;
use Electrik\Livewire\Ops\Announcements\Index as OpsAnnouncementsIndex;
use Electrik\Livewire\Ops\Dashboard as OpsDashboard;
use Electrik\Livewire\Ops\FailedJobs as OpsFailedJobs;
use Electrik\Livewire\Ops\MailPreview as OpsMailPreview;
use Electrik\Livewire\Ops\Plans as OpsPlans;
use Electrik\Livewire\Ops\Teams as OpsTeams;
use Electrik\Livewire\Ops\Users as OpsUsers;
use Electrik\Livewire\Ops\Webhooks as OpsWebhooks;
use Electrik\Livewire\Clients\Index as ClientsIndex;
use Electrik\Livewire\Pricing;
use Electrik\Livewire\Projects\Index as ProjectsIndex;
use Electrik\Livewire\Projects\Show as ProjectsShow;
use Electrik\Livewire\Settings\ApiTokens as SettingsApiTokens;
use Electrik\Livewire\Settings\Profile as SettingsProfile;
use Electrik\Livewire\Settings\Security as SettingsSecurity;
use Electrik\Livewire\Settings\Sessions as SettingsSessions;
use Electrik\Livewire\Teams\Activity as TeamsActivity;
use Electrik\Livewire\Teams\AcceptInvitation;
use Electrik\Livewire\Teams\Create;
use Electrik\Livewire\Teams\DenyInvitation;
use Electrik\Livewire\Teams\Index;
use Electrik\Livewire\Teams\Invite;
use Electrik\Livewire\Teams\Members;
use Electrik\Livewire\Teams\Permissions\Index as PermissionsIndex;
use Electrik\Livewire\Teams\Roles\Create as RolesCreate;
use Electrik\Livewire\Teams\Roles\Edit as RolesEdit;
use Electrik\Livewire\Teams\Roles\Index as RolesIndex;
use Electrik\Livewire\Teams\Settings;
use Electrik\Livewire\Teams\Switcher;
use Electrik\Livewire\Teams\Webhooks as TeamsWebhooks;
use Electrik\Models\Permission;
use Electrik\Models\Role;
use Electrik\Models\Team;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;
use Laravel\Cashier\Cashier;
use Livewire\Livewire;
use Mpociot\Teamwork\Events\UserJoinedTeam;

class ElectrikServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/electrik.php', 'electrik');

        // Always take version from this package's composer.json so release bumps
        // cannot leave config (or CI) stuck on an old hardcoded string.
        $this->app['config']->set('electrik.version', static::packageVersion());
    }

    public static function packageVersion(): string
    {
        $composerFile = dirname(__DIR__).'/composer.json';
        if (! is_readable($composerFile)) {
            return '5.x';
        }

        $composer = json_decode((string) file_get_contents($composerFile), true);

        return is_string($composer['version'] ?? null) ? $composer['version'] : '5.x';
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'electrik');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'electrik');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->configurePermissionModels();
        $this->configureActivitylogModel();
        $this->configurePasskeys();
        $this->registerMiddleware();
        $this->registerBreadcrumbs();
        $this->registerLivewireComponents();
        $this->registerRoutes();
        $this->registerListeners();
        $this->registerForbiddenViews();

        if (class_exists(Cashier::class)) {
            Cashier::useCustomerModel(Team::class);
        }

        if (class_exists(\Mpociot\Teamwork\TeamworkServiceProvider::class)) {
            config([
                'teamwork.invite_model' => \Mpociot\Teamwork\TeamInvite::class,
            ]);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                MakeLivewireCommand::class,
                MakeModelCommand::class,
                MakeResourceCommand::class,
                SyncStripeCommand::class,
                SyncSubscriptionsCommand::class,
                SyncPermissionsCommand::class,
                ResetOnboardingCommand::class,
                SeedDemoCommand::class,
                SkipOnboardingForExistingCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/electrik.php' => config_path('electrik.php'),
            ], 'electrik-config');
        }
    }

    protected function configurePasskeys(): void
    {
        if (! class_exists(\Laravel\Passkeys\PasskeysServiceProvider::class)) {
            return;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        $origins = array_values(array_unique(array_filter([
            $appUrl,
            str_replace('://127.0.0.1', '://localhost', $appUrl),
            str_replace('://localhost', '://127.0.0.1', $appUrl),
        ])));

        config([
            'passkeys.redirect' => config('electrik.auth.home', '/dashboard'),
            // Registration routes already sit behind auth; avoid password.confirm.
            'passkeys.management_middleware' => [],
            'passkeys.allowed_origins' => $origins,
        ]);
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        if (method_exists($router, 'aliasMiddleware')) {
            $router->aliasMiddleware('electrik.plan', EnsurePlanFeature::class);
            $router->aliasMiddleware('electrik.locale', SetLocale::class);
            $router->aliasMiddleware('electrik.token-team', BindTeamFromAccessToken::class);
            $router->aliasMiddleware('electrik.ability', EnsureTokenAbility::class);
            $router->aliasMiddleware('electrik.operator', EnsureOperator::class);
            $router->aliasMiddleware('electrik.team-ip', EnsureTeamIpAllowed::class);
        }

        // Laravel 11+ rebuilds middleware groups during bootstrap — append after boot.
        $this->app->booted(function () use ($router): void {
            if (! method_exists($router, 'pushMiddlewareToGroup')) {
                return;
            }

            $web = $router->getMiddlewareGroups()['web'] ?? [];
            if (! in_array(SetLocale::class, $web, true)) {
                $router->pushMiddlewareToGroup('web', SetLocale::class);
            }

            $groups = $router->getMiddlewareGroups();

            if (array_key_exists('api', $groups)) {
                $api = $groups['api'];

                if (! in_array(BindTeamFromAccessToken::class, $api, true)) {
                    $router->pushMiddlewareToGroup('api', BindTeamFromAccessToken::class);
                }

                if (! $this->apiGroupHasThrottle($api)) {
                    $this->ensureApiRateLimiterDefined();
                    $router->pushMiddlewareToGroup('api', 'throttle:api');
                }
            }
        });
    }

    /**
     * @param  array<int, mixed>  $middleware
     */
    protected function apiGroupHasThrottle(array $middleware): bool
    {
        foreach ($middleware as $entry) {
            if (is_string($entry) && ($entry === 'throttle:api' || str_starts_with($entry, 'throttle:api,'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * `throttle:api` blows up with a MissingRateLimiterException unless something
     * has registered a limiter named "api" (normally `php artisan install:api`).
     * Define a sane default only when the host app hasn't defined one already,
     * so enabling the group never 500s a host app that skipped that step.
     */
    protected function ensureApiRateLimiterDefined(): void
    {
        $limiter = $this->app->make(\Illuminate\Cache\RateLimiter::class);

        if ($limiter->limiter('api') !== null) {
            return;
        }

        $limiter->for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->getAuthIdentifier() ?: $request->ip());
        });
    }

    protected function registerBreadcrumbs(): void
    {
        if (! class_exists(\Diglactic\Breadcrumbs\Breadcrumbs::class)) {
            return;
        }

        config([
            'breadcrumbs.view' => 'electrik::breadcrumbs',
        ]);

        $this->app->booted(function () {
            $file = __DIR__.'/../routes/breadcrumbs.php';

            if (is_file($file)) {
                require $file;
            }
        });
    }

    protected function configurePermissionModels(): void
    {
        if (! class_exists(\Spatie\Permission\PermissionServiceProvider::class)) {
            return;
        }

        config([
            'permission.models.permission' => config('electrik.permissions.model', Permission::class),
            'permission.models.role' => config('electrik.permissions.role_model', Role::class),
            'permission.teams' => true,
        ]);
    }

    protected function configureActivitylogModel(): void
    {
        if (! class_exists(\Spatie\Activitylog\ActivitylogServiceProvider::class)) {
            return;
        }

        config([
            'activitylog.activity_model' => \Electrik\Models\Activity::class,
        ]);
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('electrik.dashboard', Dashboard::class);
        Livewire::component('electrik.onboarding', Onboarding::class);
        Livewire::component('electrik.pricing', Pricing::class);
        Livewire::component('electrik.projects.index', ProjectsIndex::class);
        Livewire::component('electrik.projects.show', ProjectsShow::class);
        Livewire::component('electrik.clients.index', ClientsIndex::class);
        Livewire::component('electrik.notification-bell', NotificationBell::class);

        Livewire::component('electrik.auth.login', Login::class);
        Livewire::component('electrik.auth.register', Register::class);
        Livewire::component('electrik.auth.forgot-password', ForgotPassword::class);
        Livewire::component('electrik.auth.reset-password', ResetPassword::class);
        Livewire::component('electrik.auth.verify-email', VerifyEmail::class);

        Livewire::component('electrik.teams.index', Index::class);
        Livewire::component('electrik.teams.create', Create::class);
        Livewire::component('electrik.teams.settings', Settings::class);
        Livewire::component('electrik.teams.activity', TeamsActivity::class);
        Livewire::component('electrik.teams.members', Members::class);
        Livewire::component('electrik.teams.invite', Invite::class);
        Livewire::component('electrik.teams.accept-invitation', AcceptInvitation::class);
        Livewire::component('electrik.teams.deny-invitation', DenyInvitation::class);
        Livewire::component('electrik.teams.switcher', Switcher::class);
        Livewire::component('electrik.teams.roles.index', RolesIndex::class);
        Livewire::component('electrik.teams.roles.create', RolesCreate::class);
        Livewire::component('electrik.teams.roles.edit', RolesEdit::class);
        Livewire::component('electrik.teams.permissions.index', PermissionsIndex::class);

        Livewire::component('electrik.billing.index', BillingIndex::class);
        Livewire::component('electrik.billing.plans', BillingPlans::class);
        Livewire::component('electrik.billing.subscription', BillingSubscription::class);
        Livewire::component('electrik.billing.payment-methods', BillingPaymentMethods::class);
        Livewire::component('electrik.billing.address', BillingAddress::class);
        Livewire::component('electrik.billing.invoices', BillingInvoices::class);
        Livewire::component('electrik.billing.usage', BillingUsage::class);

        Livewire::component('electrik.settings.profile', SettingsProfile::class);
        Livewire::component('electrik.settings.security', SettingsSecurity::class);
        Livewire::component('electrik.settings.sessions', SettingsSessions::class);
        Livewire::component('electrik.settings.api-tokens', SettingsApiTokens::class);

        Livewire::component('electrik.ops.dashboard', OpsDashboard::class);
        Livewire::component('electrik.ops.users', OpsUsers::class);
        Livewire::component('electrik.ops.teams', OpsTeams::class);
        Livewire::component('electrik.ops.webhooks', OpsWebhooks::class);
        Livewire::component('electrik.ops.failed-jobs', OpsFailedJobs::class);
        Livewire::component('electrik.ops.plans', OpsPlans::class);
        Livewire::component('electrik.ops.mail-preview', OpsMailPreview::class);
        Livewire::component('electrik.ops.announcements.index', OpsAnnouncementsIndex::class);
        Livewire::component('electrik.ops.announcements.form', OpsAnnouncementsForm::class);

        Livewire::component('electrik.teams.webhooks', TeamsWebhooks::class);
    }

    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->group(__DIR__.'/../routes/auth.php');

        Route::middleware('web')
            ->group(__DIR__.'/../routes/app.php');

        Route::middleware('web')
            ->group(__DIR__.'/../routes/teams.php');

        Route::middleware('web')
            ->group(__DIR__.'/../routes/billing.php');

        Route::middleware('web')
            ->group(__DIR__.'/../routes/settings.php');

        Route::middleware('web')
            ->group(__DIR__.'/../routes/ops.php');

        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__.'/../routes/api.php');

        $this->app->booted(function () {
            if (! Route::hasMacro('personalDataExports')) {
                return;
            }

            if (Route::has('personal-data-exports')) {
                return;
            }

            Route::middleware('web')->group(function () {
                Route::personalDataExports('personal-data-exports');
            });
        });
    }

    protected function registerListeners(): void
    {
        Event::listen(Registered::class, CreateDefaultTeam::class);
        Event::listen(UserJoinedTeam::class, AssignTeamRoleOnJoin::class);
        Event::listen(LoginEvent::class, SendNewLoginAlert::class);

        if (class_exists(\Laravel\Cashier\Events\WebhookReceived::class)) {
            Event::listen(\Laravel\Cashier\Events\WebhookReceived::class, [LogStripeWebhook::class, 'handleReceived']);
            Event::listen(\Laravel\Cashier\Events\WebhookHandled::class, [LogStripeWebhook::class, 'handleHandled']);
        }

        if (class_exists(TakeImpersonation::class)) {
            Event::listen(TakeImpersonation::class, [LogImpersonationActivity::class, 'handleTake']);
            Event::listen(LeaveImpersonation::class, [LogImpersonationActivity::class, 'handleLeave']);
        }

        $this->registerSocialiteProviders();
    }

    protected function registerSocialiteProviders(): void
    {
        if (! class_exists(\SocialiteProviders\Manager\SocialiteWasCalled::class)) {
            return;
        }

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event): void {
            if (class_exists(\SocialiteProviders\Apple\Provider::class)) {
                $event->extendSocialite('apple', \SocialiteProviders\Apple\Provider::class);
            }

            if (class_exists(\SocialiteProviders\Microsoft\Provider::class)) {
                $event->extendSocialite('microsoft', \SocialiteProviders\Microsoft\Provider::class);
            }
        });
    }

    protected function registerForbiddenViews(): void
    {
        $this->app->booted(function () {
            $handler = $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class);

            if (! method_exists($handler, 'renderable')) {
                return;
            }

            $handler->renderable(function (\Throwable $e, $request) {
                if ($request->expectsJson()) {
                    return null;
                }

                $status = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                    ? $e->getStatusCode()
                    : null;

                if ($status !== 403 && ! $e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return null;
                }

                return response()->view('electrik::errors.403', [
                    'message' => $e->getMessage() ?: null,
                ], 403);
            });
        });
    }
}
