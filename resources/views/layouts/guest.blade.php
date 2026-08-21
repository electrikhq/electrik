<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('electrik.name', 'Electrik') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-background text-foreground antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <div class="mb-8 text-center">
            <p class="text-sm font-medium tracking-tight text-muted-foreground">
                {{ config('electrik.name', 'Electrik') }}
            </p>
        </div>
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>
    <x-slate::toaster />
    @livewireScripts
</body>
</html>
