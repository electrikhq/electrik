<x-slate::html class="bg-white">
	<x-slot name="head">
		<title>{{ config('app.name', 'Laravel') }}</title>

		<!-- Fonts -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

		@livewireStyles
		@toastScripts
		@stack('styles')

		@vite(['resources/css/application.css', 'resources/js/application.js'])

		<script src="//js.stripe.com/v3/"></script>
	</x-slot>

	@livewire('toasts')

	<div wire:offline.class.remove="hidden" class="hidden bg-red-900">
		You are now offline.
	</div>


	@if(!auth()->user()->currentTeam->subscribed('sprrw'))
		<x-slate::banner dismissable color="info" full-width>
			<strong>You are not subscribed to any plan.</strong><br/>You must subscribe to a plan to unlock and use {{ env('APP_NAME')}}
			<x-slot name="actions">
			{{-- <a class="underline" href="{{ route('billing.subscription') }}">Click here to subscribe.</a> --}}
			</x-slot>
		</x-slate::banner>
	@endif
	<x-slate::shell  brand="{{ env('APP_NAME', '') }}" no-burger-menu class="bg-white">

		
		<x-slot name="sidebar">
			@include('electrik::includes.livewire.sidebar')
		</x-slot>

		<div class="overflow-y-auto h-full shadow-xl shadow-stone-400 z-10">

			@if(isset($slot))
				{{ $slot }}
			@else
				@yield('content')
			@endif

		</div>

	</x-slate::shell>

	<x-slot name="foot">
		{{-- modalwidth comment for tailwind purge, used widths: sm:max-w-sm sm:max-w-md sm:max-w-lg sm:max-w-xl sm:max-w-2xl sm:max-w-3xl sm:max-w-4xl sm:max-w-5xl sm:max-w-6xl sm:max-w-7xl md:max-w-sm md:max-w-md md:max-w-lg md:max-w-xl md:max-w-2xl md:max-w-3xl md:max-w-4xl md:max-w-5xl md:max-w-6xl md:max-w-7xl lg:max-w-sm lg:max-w-md lg:max-w-lg lg:max-w-xl lg:max-w-2xl lg:max-w-3xl lg:max-w-4xl lg:max-w-5xl lg:max-w-6xl lg:max-w-7xl xl:max-w-sm xl:max-w-md xl:max-w-lg xl:max-w-xl xl:max-w-2xl xl:max-w-3xl xl:max-w-4xl xl:max-w-5xl xl:max-w-6xl xl:max-w-7xl --}}
		@livewire('livewire-ui-modal')
		@livewireScripts
		@stack('scripts')
	</x-slot>

</x-slate::html>