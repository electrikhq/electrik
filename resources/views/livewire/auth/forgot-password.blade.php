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
		<x-slate::content>
			<x-slate::heading tag="h1" font-bold>Welcome Back.</x-slate::heading>
			<p>{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
		
			<x-slate::form wire:submit.prevent="submit">
				
				<x-slate::input

					wire:model.defer="email"
					type="email"
					name="email"
					id="email"
					label="Your Email Address"
					placeholder="you@yourcompany.com"
					
				/>
				<x-slot name="actions">
					<x-slate::button @click="submit" full-width color="primary" size="xl">{{ __('Email Password Reset Link') }}</x-slate::button>
				</x-slot>
			</x-slate::form>
		</x-slate::content>
	</div>
</div>
</div>