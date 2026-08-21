<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('electrik.name', 'Electrik') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-background text-foreground antialiased">
    <x-slate::app-shell class="min-h-screen">
        <x-slot:header>
            <div class="flex flex-1 items-center justify-between gap-4 px-4">
                <p class="text-sm font-medium tracking-tight">
                    {{ config('electrik.name', 'Electrik') }}
                </p>
                <x-slate::badge variant="secondary">5.x alpha</x-slate::badge>
            </div>
        </x-slot:header>

        <x-slot:sidebar>
            <aside class="flex h-full w-56 flex-col border-e border-border bg-sidebar text-sidebar-foreground">
                <div class="border-b border-sidebar-border px-3 py-3">
                    <p class="text-xs font-medium text-muted-foreground">Workspace</p>
                </div>
                <nav class="flex flex-1 flex-col gap-0.5 p-2">
                    {{ $sidebar ?? '' }}
                    @unless (isset($sidebar))
                        <span class="flex h-8 items-center rounded-md bg-sidebar-accent px-2.5 text-sm font-medium text-sidebar-accent-foreground">
                            Dashboard
                        </span>
                    @endunless
                </nav>
            </aside>
        </x-slot:sidebar>

        <x-slot:main>
            <div class="p-6">
                {{ $slot }}
            </div>
        </x-slot:main>
    </x-slate::app-shell>

    <x-slate::toaster />
    @livewireScripts
</body>
</html>
