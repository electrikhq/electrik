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

    <x-slate::shell class="bg-white">
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
