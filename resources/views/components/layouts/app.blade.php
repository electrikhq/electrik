<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \Electrik\Support\Locales::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('electrik.name', 'Electrik') }} — {{ config('electrik.name', 'Electrik') }}</title>
    <x-electrik::brand-styles :team="auth()->user()?->currentTeam" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
</head>
@php
    $currentTeam = auth()->user()?->currentTeam;
    $routeTeam = request()->route('team');
    $navTeam = ($routeTeam instanceof \Electrik\Models\Team) ? $routeTeam : $currentTeam;
    $user = auth()->user();
    $initials = $user
        ? collect(preg_split('/\s+/', trim((string) $user->name)) ?: [])
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('')
        : '?';

    $navSection = match (true) {
        request()->routeIs('billing.*') => 'billing',
        request()->routeIs('settings.*') => 'account',
        request()->routeIs('teams.*') => 'teams',
        request()->routeIs('ops.*') => 'ops',
        default => 'dashboard',
    };

    $secondaryTitle = match ($navSection) {
        'billing' => __('Billing'),
        'account' => __('Account'),
        'teams' => $navTeam?->name ?? $currentTeam?->name ?? __('Teams'),
        'ops' => __('Operations'),
        default => __('Studio'),
    };

    $brandLogo = $currentTeam?->brandLogoUrl() ?: config('electrik.branding.logo_url');
@endphp
<body class="h-svh overflow-hidden bg-background text-foreground antialiased">
    @if (auth()->check() && method_exists(auth()->user(), 'isImpersonated') && auth()->user()->isImpersonated())
        <div class="flex items-center justify-between gap-3 border-b border-amber-500/40 bg-amber-500/15 px-4 py-2 text-sm text-foreground">
            <p>
                {{ __('You are impersonating :name.', ['name' => auth()->user()?->name ?? __('a user')]) }}
            </p>
            <form method="POST" action="{{ route('impersonation.leave') }}">
                @csrf
                <x-slate::button type="submit" size="sm" variant="outline">
                    {{ __('Stop impersonating') }}
                </x-slate::button>
            </form>
        </div>
    @endif
    <x-slate::app-shell :default-open="true">
        <x-slot:header>
            <div class="flex flex-1 items-center justify-between gap-4">
                <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
                    <x-slate::button
                        type="button"
                        variant="ghost"
                        size="sm"
                        class="shrink-0"
                        x-on:click="open = !open"
                        aria-label="{{ __('Toggle secondary sidebar') }}"
                    >
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </x-slate::button>

                    <div class="flex min-w-0 items-center gap-2 overflow-hidden">
                        <a href="{{ route('dashboard') }}" class="shrink-0 text-sm font-semibold tracking-tight text-foreground" wire:navigate>
                            <x-electrik::brand-mark />
                        </a>

                        <span class="shrink-0 text-muted-foreground" aria-hidden="true">/</span>

                        <div class="min-w-0 overflow-hidden">
                            <x-electrik::breadcrumbs />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        @if (\Illuminate\Support\Facades\Schema::hasTable('notifications'))
                            <livewire:electrik.notification-bell />
                        @endif

                        <x-slate::dropdown-menu>
                            <x-slate::dropdown-menu-trigger>
                                <x-slate::button type="button" variant="ghost" size="sm" class="gap-2">
                                    <x-slate::avatar size="sm" :fallback="$initials" :alt="$user->name" />
                                    <span class="hidden max-w-[10rem] truncate sm:inline">{{ $user->name }}</span>
                                </x-slate::button>
                            </x-slate::dropdown-menu-trigger>
                            <x-slate::dropdown-menu-content align="end" class="w-48">
                                <x-slate::dropdown-menu-item as="a" href="{{ route('settings.profile') }}" wire:navigate>
                                    {{ __('Profile') }}
                                </x-slate::dropdown-menu-item>
                                <x-slate::dropdown-menu-item as="a" href="{{ route('settings.security') }}" wire:navigate>
                                    {{ __('Security') }}
                                </x-slate::dropdown-menu-item>
                                <x-slate::dropdown-menu-item as="a" href="{{ route('settings.sessions') }}" wire:navigate>
                                    {{ __('Sessions') }}
                                </x-slate::dropdown-menu-item>
                                @if (class_exists(\Laravel\Sanctum\SanctumServiceProvider::class))
                                    <x-slate::dropdown-menu-item as="a" href="{{ route('settings.api-tokens') }}" wire:navigate>
                                        {{ __('API tokens') }}
                                    </x-slate::dropdown-menu-item>
                                @endif
                                <x-slate::dropdown-menu-separator />
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-slate::dropdown-menu-item as="button" type="submit" variant="destructive">
                                        {{ __('Sign out') }}
                                    </x-slate::dropdown-menu-item>
                                </form>
                            </x-slate::dropdown-menu-content>
                        </x-slate::dropdown-menu>
                    @endauth
                </div>
            </div>
        </x-slot:header>

        <x-slot:primary>
            <aside class="flex h-full w-14 flex-col items-center border-e border-sidebar-border bg-sidebar py-3 text-sidebar-foreground">
                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    class="mb-4 inline-flex size-9 items-center justify-center overflow-hidden rounded-lg bg-foreground text-xs font-bold tracking-tight text-background"
                    aria-label="{{ config('electrik.name', 'Electrik') }}"
                    title="{{ config('electrik.name', 'Electrik') }}"
                >
                    @if (filled($brandLogo))
                        <img src="{{ $brandLogo }}" alt="" class="size-9 object-cover" />
                    @else
                        {{ mb_strtoupper(mb_substr(config('electrik.name', 'E'), 0, 1)) }}
                    @endif
                </a>

                <nav class="flex flex-1 flex-col items-center gap-1" aria-label="{{ __('Primary') }}">
                    <x-electrik::rail-link
                        :href="route('dashboard')"
                        :active="$navSection === 'dashboard'"
                        :label="__('Dashboard')"
                    >
                        @svg('carbon-dashboard', 'size-5')
                    </x-electrik::rail-link>

                    <x-electrik::rail-link
                        :href="$navTeam ? route('teams.members', $navTeam) : ($currentTeam ? route('teams.members', $currentTeam) : route('teams.index'))"
                        :active="$navSection === 'teams'"
                        :label="__('Teams')"
                    >
                        @svg('carbon-enterprise', 'size-5')
                    </x-electrik::rail-link>

                    <x-electrik::rail-link
                        :href="route('billing.index')"
                        :active="$navSection === 'billing'"
                        :label="__('Billing')"
                    >
                        @svg('carbon-wallet', 'size-5')
                    </x-electrik::rail-link>

                    @auth
                        @if (\Electrik\Support\Operators::check(auth()->user()))
                            <x-electrik::rail-link
                                :href="route('ops.dashboard')"
                                :active="$navSection === 'ops'"
                                :label="__('Operations')"
                            >
                                @svg('carbon-operations-record', 'size-5')
                            </x-electrik::rail-link>
                        @endif
                    @endauth
                </nav>
            </aside>
        </x-slot:primary>

        <x-slot:sidebar>
            <aside class="flex h-full w-60 flex-col border-e border-sidebar-border bg-sidebar text-sidebar-foreground">
                <div class="border-b border-sidebar-border px-4 py-4">
                    <p class="text-[11px] font-medium uppercase tracking-wider text-muted-foreground">
                        {{ $navSection === 'teams' ? __('Workspace') : __('Navigate') }}
                    </p>
                    <p class="mt-1 truncate text-sm font-semibold tracking-tight">
                        {{ $secondaryTitle }}
                    </p>
                </div>

                <nav class="flex flex-1 flex-col gap-6 overflow-y-auto p-3" aria-label="{{ __('Secondary') }}">
                    {{ $sidebar ?? '' }}
                    @unless (isset($sidebar))
                        @if ($navSection === 'dashboard')
                            <div class="space-y-1">
                                <p class="px-2.5 pb-1 text-[11px] font-medium uppercase tracking-wider text-muted-foreground">{{ __('Studio') }}</p>
                                <x-electrik::nav-link :href="route('dashboard')" :exact="true" icon="dashboard">{{ __('Overview') }}</x-electrik::nav-link>
                                @if (\Electrik\Support\SampleStudio::enabled() && \Illuminate\Support\Facades\Route::has('clients.index'))
                                    <x-electrik::nav-link :href="route('clients.index')" icon="user">{{ __('Clients') }}</x-electrik::nav-link>
                                    <x-electrik::nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')" icon="folder">{{ __('Projects') }}</x-electrik::nav-link>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <p class="px-2.5 pb-1 text-[11px] font-medium uppercase tracking-wider text-muted-foreground">{{ __('Workspace') }}</p>
                                <x-electrik::nav-link :href="route('teams.index')" icon="enterprise">{{ __('All teams') }}</x-electrik::nav-link>
                            </div>
                        @elseif ($navSection === 'teams')
                            <div class="space-y-1">
                                <x-electrik::nav-link :href="route('teams.index')" :active="request()->routeIs('teams.index', 'teams.create')" icon="enterprise">{{ __('All teams') }}</x-electrik::nav-link>
                                @auth
                                    @if ($navTeam ?? $currentTeam)
                                        @php($sidebarTeam = $navTeam ?? $currentTeam)
                                        <x-electrik::nav-link
                                            :href="route('teams.members', $sidebarTeam)"
                                            :active="request()->routeIs('teams.members', 'teams.members.*')"
                                            icon="user-multiple"
                                        >
                                            {{ __('Members') }}
                                        </x-electrik::nav-link>
                                        @if (auth()->user()->can('access.roles') || auth()->user()->isOwnerOfTeam($sidebarTeam))
                                            <x-electrik::nav-link
                                                :href="route('teams.roles.index', $sidebarTeam)"
                                                :active="request()->routeIs('teams.roles.*', 'teams.permissions.*')"
                                                icon="user-role"
                                            >
                                                {{ __('Roles') }}
                                            </x-electrik::nav-link>
                                        @endif
                                        @if (auth()->user()->can('teams.manage') || auth()->user()->isOwnerOfTeam($sidebarTeam))
                                            <x-electrik::nav-link
                                                :href="route('teams.settings', $sidebarTeam)"
                                                :active="request()->routeIs('teams.settings')"
                                                icon="settings"
                                            >
                                                {{ __('Settings') }}
                                            </x-electrik::nav-link>
                                            <x-electrik::nav-link
                                                :href="route('teams.activity', $sidebarTeam)"
                                                :active="request()->routeIs('teams.activity')"
                                                icon="report"
                                            >
                                                {{ __('Activity') }}
                                            </x-electrik::nav-link>
                                            <x-electrik::nav-link
                                                :href="route('teams.webhooks', $sidebarTeam)"
                                                :active="request()->routeIs('teams.webhooks')"
                                                icon="connect"
                                            >
                                                {{ __('Webhooks') }}
                                            </x-electrik::nav-link>
                                        @endif
                                    @endif
                                @endauth
                            </div>
                        @elseif ($navSection === 'billing')
                            <div class="space-y-1">
                                <x-electrik::nav-link :href="route('billing.index')" :exact="true" icon="dashboard">{{ __('Overview') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.plans')" icon="catalog">{{ __('Plans') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.subscription')" icon="renew">{{ __('Subscription') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.payment-methods')" icon="purchase">{{ __('Payment methods') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.address')" icon="location">{{ __('Address') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.invoices')" icon="receipt">{{ __('Invoices') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.usage')" icon="analytics">{{ __('Usage') }}</x-electrik::nav-link>
                            </div>
                        @elseif ($navSection === 'account')
                            <div class="space-y-1">
                                <x-electrik::nav-link :href="route('settings.profile')" :active="request()->routeIs('settings.profile')" icon="user">{{ __('Profile') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('settings.security')" :active="request()->routeIs('settings.security')" icon="password">{{ __('Security') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('settings.sessions')" :active="request()->routeIs('settings.sessions')" icon="devices">{{ __('Sessions') }}</x-electrik::nav-link>
                                @if (class_exists(\Laravel\Sanctum\SanctumServiceProvider::class))
                                    <x-electrik::nav-link :href="route('settings.api-tokens')" :active="request()->routeIs('settings.api-tokens')" icon="connect">{{ __('API tokens') }}</x-electrik::nav-link>
                                @endif
                            </div>
                        @elseif ($navSection === 'ops')
                            <div class="space-y-1">
                                <x-electrik::nav-link :href="route('ops.dashboard')" :exact="true" icon="dashboard">{{ __('Dashboard') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('ops.users')" icon="user-multiple">{{ __('Users') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('ops.teams')" icon="enterprise">{{ __('Teams') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('ops.webhooks')" icon="connect">{{ __('Webhooks') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('ops.plans')" icon="catalog">{{ __('Plan features') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('ops.failed-jobs')" icon="renew">{{ __('Failed jobs') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('ops.announcements.index')" :active="request()->routeIs('ops.announcements.*')" icon="bullhorn">{{ __('Announcements') }}</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('ops.mail-preview')" icon="email">{{ __('Email preview') }}</x-electrik::nav-link>
                            </div>
                        @endif
                    @endunless
                </nav>

                <div class="mt-auto border-t border-sidebar-border p-3">
                    @auth
                        @if (method_exists(auth()->user(), 'teams') && $navSection === 'teams')
                            <livewire:electrik.teams.switcher />
                        @endif
                    @endauth
                    <p class="mt-3 px-2.5 text-[10px] text-muted-foreground/80">
                        {{ config('electrik.name') }} {{ config('electrik.version') }}
                    </p>
                </div>
            </aside>
        </x-slot:sidebar>

        <x-slot:main>
            <x-electrik::subscription-banner />
            <x-electrik::announcement-banner />
            <div class="min-h-full bg-muted/30">
                <div class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </div>
        </x-slot:main>
    </x-slate::app-shell>

    <x-slate::toaster />
    @livewireScripts
</body>
</html>
