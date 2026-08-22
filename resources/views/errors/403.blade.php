<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Access denied') }} — {{ config('electrik.name', 'Electrik') }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-svh bg-background text-foreground antialiased">
    <div class="flex min-h-svh items-center justify-center px-4 py-12">
        <div class="mx-auto max-w-md space-y-4 text-center">
            <p class="text-6xl font-semibold tracking-tight text-muted-foreground/40">403</p>
            <h1 class="text-xl font-semibold tracking-tight">{{ __('Access denied') }}</h1>
            <p class="text-sm text-muted-foreground">
                {{ $message ?? __('You do not have permission to view this page.') }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-2 pt-2">
                <a href="{{ route('dashboard') }}" class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground">
                    {{ __('Go to dashboard') }}
                </a>
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('teams.index') }}" class="inline-flex h-9 items-center justify-center rounded-md border px-4 text-sm font-medium">
                    {{ __('Go back') }}
                </a>
            </div>
        </div>
    </div>
</body>
</html>
