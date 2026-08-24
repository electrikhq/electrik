<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('electrik.name', 'Electrik') }} — {{ config('electrik.name', 'Electrik') }}</title>
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
        default => 'dashboard',
    };

    $secondaryTitle = match ($navSection) {
        'billing' => __('Billing'),
        'account' => __('Account'),
        'teams' => $navTeam?->name ?? $currentTeam?->name ?? __('Teams'),
        default => __('Workspace'),
    };
@endphp
<body class="h-svh overflow-hidden bg-background text-foreground antialiased">
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
                            {{ config('electrik.name', 'Electrik') }}
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

                        <x-slate::dropdown-menu class="hidden sm:inline-flex">
                            <x-slate::dropdown-menu-trigger>
                                <x-slate::button type="button" variant="ghost" size="sm" class="gap-2">
                                    <x-slate::avatar size="sm" :fallback="$initials" :alt="$user->name" />
                                    <span class="max-w-[10rem] truncate">{{ $user->name }}</span>
                                </x-slate::button>
                            </x-slate::dropdown-menu-trigger>
                            <x-slate::dropdown-menu-content align="end" class="w-48">
                                <x-slate::dropdown-menu-item as="a" href="{{ route('settings.profile') }}" wire:navigate>
                                    Profile
                                </x-slate::dropdown-menu-item>
                                <x-slate::dropdown-menu-item as="a" href="{{ route('settings.security') }}" wire:navigate>
                                    Security
                                </x-slate::dropdown-menu-item>
                                <x-slate::dropdown-menu-item as="a" href="{{ route('settings.sessions') }}" wire:navigate>
                                    Sessions
                                </x-slate::dropdown-menu-item>
                                @if (class_exists(\Laravel\Sanctum\SanctumServiceProvider::class))
                                    <x-slate::dropdown-menu-item as="a" href="{{ route('settings.api-tokens') }}" wire:navigate>
                                        API tokens
                                    </x-slate::dropdown-menu-item>
                                @endif
                                <x-slate::dropdown-menu-separator />
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-slate::dropdown-menu-item as="button" type="submit" variant="destructive">
                                        Sign out
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
                    class="mb-4 inline-flex size-9 items-center justify-center rounded-lg bg-foreground text-xs font-bold tracking-tight text-background"
                    aria-label="{{ config('electrik.name', 'Electrik') }}"
                    title="{{ config('electrik.name', 'Electrik') }}"
                >
                    {{ mb_strtoupper(mb_substr(config('electrik.name', 'E'), 0, 1)) }}
                </a>

                <nav class="flex flex-1 flex-col items-center gap-1" aria-label="{{ __('Primary') }}">
                    <x-electrik::rail-link
                        :href="route('dashboard')"
                        :active="$navSection === 'dashboard'"
                        :label="__('Dashboard')"
                    >
                        <x-electrik::icon name="dashboard" class="size-5" />
                    </x-electrik::rail-link>

                    <x-electrik::rail-link
                        :href="$navTeam ? route('teams.members', $navTeam) : ($currentTeam ? route('teams.members', $currentTeam) : route('teams.index'))"
                        :active="$navSection === 'teams'"
                        :label="__('Teams')"
                    >
                        <x-electrik::icon name="enterprise" class="size-5" />
                    </x-electrik::rail-link>

                    <x-electrik::rail-link
                        :href="route('billing.index')"
                        :active="$navSection === 'billing'"
                        :label="__('Billing')"
                    >
                        <x-electrik::icon name="wallet" class="size-5" />
                    </x-electrik::rail-link>
                </nav>

                <div class="mt-auto flex flex-col items-center gap-1">
                    <x-slate::dropdown-menu>
                        <x-slate::dropdown-menu-trigger>
                            <button
                                type="button"
                                @class([
                                    'inline-flex size-10 items-center justify-center rounded-lg transition-colors',
                                    'bg-sidebar-accent text-sidebar-accent-foreground' => $navSection === 'account',
                                    'text-muted-foreground hover:bg-sidebar-accent/70 hover:text-sidebar-accent-foreground' => $navSection !== 'account',
                                ])
                                aria-label="{{ __('Account') }}"
                                title="{{ __('Account') }}"
                            >
                                <x-electrik::icon name="user" class="size-5" />
                            </button>
                        </x-slate::dropdown-menu-trigger>
                        <x-slate::dropdown-menu-content side="end" align="start" class="w-44">
                            <x-slate::dropdown-menu-item as="a" href="{{ route('settings.profile') }}" wire:navigate>
                                Profile
                            </x-slate::dropdown-menu-item>
                            <x-slate::dropdown-menu-item as="a" href="{{ route('settings.security') }}" wire:navigate>
                                Security
                            </x-slate::dropdown-menu-item>
                            <x-slate::dropdown-menu-item as="a" href="{{ route('settings.sessions') }}" wire:navigate>
                                Sessions
                            </x-slate::dropdown-menu-item>
                            @if (class_exists(\Laravel\Sanctum\SanctumServiceProvider::class))
                                <x-slate::dropdown-menu-item as="a" href="{{ route('settings.api-tokens') }}" wire:navigate>
                                    API tokens
                                </x-slate::dropdown-menu-item>
                            @endif
                            <x-slate::dropdown-menu-separator />
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-slate::dropdown-menu-item as="button" type="submit" variant="destructive">
                                    Sign out
                                </x-slate::dropdown-menu-item>
                            </form>
                        </x-slate::dropdown-menu-content>
                    </x-slate::dropdown-menu>
                </div>
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
                                <x-electrik::nav-link :href="route('dashboard')" :exact="true" icon="dashboard">Dashboard</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('teams.index')" icon="enterprise">All teams</x-electrik::nav-link>
                            </div>
                        @elseif ($navSection === 'teams')
                            <div class="space-y-1">
                                <x-electrik::nav-link :href="route('teams.index')" :active="request()->routeIs('teams.index', 'teams.create')" icon="enterprise">All teams</x-electrik::nav-link>
                                @auth
                                    @if ($navTeam ?? $currentTeam)
                                        @php($sidebarTeam = $navTeam ?? $currentTeam)
                                        <x-electrik::nav-link
                                            :href="route('teams.members', $sidebarTeam)"
                                            :active="request()->routeIs('teams.members', 'teams.members.*')"
                                            icon="user-multiple"
                                        >
                                            Members
                                        </x-electrik::nav-link>
                                        @if (auth()->user()->can('access.roles') || auth()->user()->isOwnerOfTeam($sidebarTeam))
                                            <x-electrik::nav-link
                                                :href="route('teams.roles.index', $sidebarTeam)"
                                                :active="request()->routeIs('teams.roles.*', 'teams.permissions.*')"
                                                icon="user-role"
                                            >
                                                Roles
                                            </x-electrik::nav-link>
                                        @endif
                                        @if (auth()->user()->can('teams.manage') || auth()->user()->isOwnerOfTeam($sidebarTeam))
                                            <x-electrik::nav-link
                                                :href="route('teams.settings', $sidebarTeam)"
                                                :active="request()->routeIs('teams.settings')"
                                                icon="settings"
                                            >
                                                Settings
                                            </x-electrik::nav-link>
                                            <x-electrik::nav-link
                                                :href="route('teams.activity', $sidebarTeam)"
                                                :active="request()->routeIs('teams.activity')"
                                                icon="report"
                                            >
                                                Activity
                                            </x-electrik::nav-link>
                                        @endif
                                    @endif
                                @endauth
                            </div>
                        @elseif ($navSection === 'billing')
                            <div class="space-y-1">
                                <x-electrik::nav-link :href="route('billing.index')" :exact="true" icon="dashboard">Overview</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.plans')" icon="catalog">Plans</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.subscription')" icon="renew">Subscription</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.payment-methods')" icon="purchase">Payment methods</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.address')" icon="location">Address</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('billing.invoices')" icon="receipt">Invoices</x-electrik::nav-link>
                            </div>
                        @elseif ($navSection === 'account')
                            <div class="space-y-1">
                                <x-electrik::nav-link :href="route('settings.profile')" :active="request()->routeIs('settings.profile')" icon="user">Profile</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('settings.security')" :active="request()->routeIs('settings.security')" icon="password">Security</x-electrik::nav-link>
                                <x-electrik::nav-link :href="route('settings.sessions')" :active="request()->routeIs('settings.sessions')" icon="devices">Sessions</x-electrik::nav-link>
                                @if (class_exists(\Laravel\Sanctum\SanctumServiceProvider::class))
                                    <x-electrik::nav-link :href="route('settings.api-tokens')" :active="request()->routeIs('settings.api-tokens')" icon="connect">API tokens</x-electrik::nav-link>
                                @endif
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
