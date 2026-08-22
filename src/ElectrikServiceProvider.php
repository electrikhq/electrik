<?php

namespace Electrik;

use Electrik\Console\InstallCommand;
use Electrik\Console\ResetOnboardingCommand;
use Electrik\Console\SeedDemoCommand;
use Electrik\Console\SkipOnboardingForExistingCommand;
use Electrik\Console\SyncPermissionsCommand;
use Electrik\Console\SyncStripeCommand;
use Electrik\Console\SyncSubscriptionsCommand;
use Electrik\Http\Middleware\EnsurePlanFeature;
use Electrik\Listeners\AssignTeamRoleOnJoin;
use Electrik\Listeners\CreateDefaultTeam;
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
use Electrik\Livewire\Dashboard;
use Electrik\Livewire\NotificationBell;
use Electrik\Livewire\Onboarding;
use Electrik\Livewire\Pricing;
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
use Electrik\Models\Permission;
use Electrik\Models\Role;
use Electrik\Models\Team;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Livewire\Livewire;
use Mpociot\Teamwork\Events\UserJoinedTeam;

class ElectrikServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/electrik.php', 'electrik');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'electrik');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'electrik');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->configurePermissionModels();
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
                'teamwork.invite_model' => \Electrik\Models\TeamInvite::class,
            ]);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
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

    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        if (method_exists($router, 'aliasMiddleware')) {
            $router->aliasMiddleware('electrik.plan', EnsurePlanFeature::class);
        }
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

    protected function registerLivewireComponents(): void
    {
        Livewire::component('electrik.dashboard', Dashboard::class);
        Livewire::component('electrik.onboarding', Onboarding::class);
        Livewire::component('electrik.pricing', Pricing::class);
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

        Livewire::component('electrik.settings.profile', SettingsProfile::class);
        Livewire::component('electrik.settings.security', SettingsSecurity::class);
        Livewire::component('electrik.settings.sessions', SettingsSessions::class);
        Livewire::component('electrik.settings.api-tokens', SettingsApiTokens::class);
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
    }

    protected function registerListeners(): void
    {
        Event::listen(Registered::class, CreateDefaultTeam::class);
        Event::listen(UserJoinedTeam::class, AssignTeamRoleOnJoin::class);
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
