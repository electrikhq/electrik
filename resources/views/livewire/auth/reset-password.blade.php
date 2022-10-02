<div>
	<div class="">
	<div class="flex space-x-12 py-8 px-6 justify-between items-center">
		<a class="text-lg font-bold" href="#" onlick="return false;" aria-current="page">{{ env("APP_NAME") }}</a>

		<x-slate::button color="primary" :link="route('onboarding.choose.plan')">
			Create Free Account
		</x-slate::button>
	</div>
</div>

<div class="flex h-full flex-col items-center justify-items-center">
	<div class="w-full sm:w-1/3 px-6 mt-24">

	{{ $errors }}
		<x-slate::heading tag="h1" font-bold>Reset password?</x-slate::heading>
		<p>Enter your new password bbelow</p>
	
		<x-slate::form wire:submit.prevent="submit">

			@csrf
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
				type="password"
				name="password"
				id="password"
				:label="__('Password')"
				placeholder="password"
				
			/>

			<x-slate::input

				wire:model.defer="password_confirmation"
				type="password"
				name="password_confirmation"
				id="password_confirmation"
				:label="__('Confirm Password')"
				placeholder="confirm password"
				
			/>

			<x-slot name="actions">
				<x-slate::button @click="submit" full-width color="primary" size="xl">{{ __('Reset Password') }}</x-slate::button>
			</x-slot>
		</x-slate::form>
		<p><x-slate::link :href="route('login')">Login?</x-slate::link></p>

	</div>
</div>
</div>