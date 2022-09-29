<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        @livewireStyles
		@toastScripts
		@livewireScripts
		@stack('styles')
		@stack('scripts')

		@vite(['resources/css/application.css', 'resources/js/application.js'])
        
    </head>
	<body class="font-sans text-gray-900 antialiased h-screen">
		<livewire:toasts />
		<div class="overflow-y-auto">
			{{ $slot }}
		</div>
		
		
    </body>
</html>
