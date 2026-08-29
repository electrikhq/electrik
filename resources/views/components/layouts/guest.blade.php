<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \Electrik\Support\Locales::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('electrik.name', 'Electrik') }}</title>
    <x-electrik::brand-styles />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
</head>
<body class="min-h-screen bg-background text-foreground antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <div class="mb-8 text-center">
            <x-electrik::brand-mark class="justify-center text-sm font-medium tracking-tight text-muted-foreground" />
        </div>
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>
    <x-slate::toaster />
    @livewireScripts
</body>
</html>
