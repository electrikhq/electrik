<div>
	<div class="">
	<div class="flex space-x-12 py-8 px-6 justify-between items-center">
		<a class="text-lg font-bold" href="#" onlick="return false;" aria-current="page">{{ env("APP_NAME") }}</a>
	</div>
</div>

<div class="flex h-full flex-col items-center justify-items-center">
	<div class="w-full sm:w-1/3 px-6 mt-24">
		<x-slate::content>

			<x-slate::heading tag="h1" font-bold>{{ $invite->team->name }} has invited you to join their team.</x-slate::heading>

			<p class="text-lg font-medium text-gray-700">Login to your account to join.</p>
		

			<x-slate::form wire:submit.prevent="login">
				
				<x-slate::input

					wire:model.defer="email"
					type="email"
					name="email"
					id="email"
					label="Your Email Address"
					placeholder="you@yourcompany.com"
					
				/>
				<x-slate::input

					wire:model.defer="password"
					id="password"
					name="password"
					type="password"
					label="Password"
					placeholder="password"
					current-password
					
				/>
				<x-slot name="actions">
					<x-slate::button
						@click="login"
						full-width
						color="primary"
						size="xl"
					>
					LOGIN
					</x-slate::button>
				</x-slot>
			</x-slate::form>
			<p>Do not have have an account? <a href="{{ route('teams.invite.register') }}">Click here to create one</a>.</p>
		</x-slate::content>
	</div>
</div>
</div>