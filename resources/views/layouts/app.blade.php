<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-white dark:bg-black">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'Electrik') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @toastScripts
    @stack('styles')
</head>

<body class="bg-white dark:bg-black antialiased">
    <livewire:toasts />

    <x-slate::shell brand="{{ config('app.name', 'Electrik') }}" no-burger-menu class="bg-white">
        @if(auth()->check())
            <x-slot name="primarySidebar">
                @include('includes.livewire.primary-sidebar')
            </x-slot>

            @php
                // Get current route name
                $routeName = request()->route()?->getName();

                // Routes that should not show the secondary sidebar
                $routesWithoutSidebar = ['dashboard.index'];

                $shouldShowSidebar = true;
                if ($routeName) {
                    // Check if current route matches any route pattern that shouldn't show sidebar
                    foreach ($routesWithoutSidebar as $pattern) {
                        if (request()->routeIs($pattern)) {
                            $shouldShowSidebar = false;
                            break;
                        }
                    }

                    if ($shouldShowSidebar) {
                        // Map account/settings routes to use settings sidebar
                        $sidebarView = null;
                        if (str_starts_with($routeName, 'settings.') || str_starts_with($routeName, 'account.')) {
                            if (view()->exists('includes.livewire.sidebar.settings')) {
                                $sidebarView = 'includes.livewire.sidebar.settings';
                            }
                        } else {
                            // Split route name into segments
                            $segments = explode('.', $routeName);

                            // Try progressively shorter paths (most specific to least specific)
                            $paths = [];
                            $currentPath = 'includes.livewire.sidebar';

                            // Remove the last segment (usually the action like 'index', 'settings', 'create')
                            // and build paths for each level
                            for ($i = 0; $i < count($segments) - 1; $i++) {
                                $currentPath .= '.' . $segments[$i];
                                $paths[] = $currentPath;
                            }

                            // Reverse order to check most specific first
                            $paths = array_reverse($paths);

                            // Check if any of these views exist
                            foreach ($paths as $path) {
                                if (view()->exists($path)) {
                                    $sidebarView = $path;
                                    break;
                                }
                            }
                        }

                        // Only show sidebar if we found a sidebar view
                        $shouldShowSidebar = !empty($sidebarView);
                    }
                } else {
                    $shouldShowSidebar = false;
                }
            @endphp

            @if($shouldShowSidebar)
                <x-slot name="sidebar">
                    @include($sidebarView)
                </x-slot>
            @endif
        @endif

        <div class="overflow-y-auto h-full shadow-xs shadow-stone-400 z-10">
            @if(isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif

            <div class="h-24"></div>
        </div>
    </x-slate::shell>

    @livewire('wire-elements-modal')
    @livewireScriptConfig
    @stack('scripts')
</body>
</html>
