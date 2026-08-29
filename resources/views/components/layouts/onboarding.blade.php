<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \Electrik\Support\Locales::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('Get started') }} — {{ config('electrik.name', 'Electrik') }}</title>
    <x-electrik::brand-styles />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
</head>
<body class="min-h-svh bg-muted/30 text-foreground antialiased">
    <div class="mx-auto flex min-h-svh max-w-lg flex-col justify-center px-4 py-12">
        <div class="mb-8 text-center">
            <p class="text-sm font-semibold tracking-tight"><x-electrik::brand-mark /></p>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Set up your workspace') }}</p>
        </div>

        {{ $slot }}

        <p class="mt-8 text-center text-[10px] text-muted-foreground/80">
            {{ config('electrik.name') }} {{ config('electrik.version') }}
        </p>
    </div>

    <x-slate::toaster />
    @livewireScripts
</body>
</html>
