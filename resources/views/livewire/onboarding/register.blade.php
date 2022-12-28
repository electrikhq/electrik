<div class="flex flex-col h-screen w-full">

<div class="bg-white dark:bg-stone-800">
		<div class="flex space-x-12 py-6 pl-6 border-b border-gray-200 dark:border-stone-700">
			<a class="text-lg font-bold" href="#" onlick="return false;" aria-current="page">{{ env("APP_NAME") }}</a>

			<a class="text-lg font-medium text-gray-600" href="#" onclick="return false;">1. Choose Plan</a>
			
			<a class="text-lg font-bold text-primary-600 " href="#" onclick="return false;">2. Create Account</a>

			<a class="text-lg font-medium text-gray-600" href="#" onclick="return false;">3. Confrim Details</a>
		</div>
	</div>
	<div class="flex h-full dark:bg-stone-900">
		<div class="flex-1 flex bg--50 items-center">
			<div class="flex-1 mx-12 xs:mx-12 sm:mx-24 md:mx-24 ">
				
					<x-slate::heading>Create your account</x-slate::heading>
					<p>Fill the form below to get started</p>
				
				<x-slate::form wire:submit.prevent="submit" class="">
					<x-slate::input
						wire:model.defer="name"
						type="text"
						name="name"
						id="name"
						label="Full Name"
						placeholder="First and Last Name"
						autocomplete="name"
					/>
					<x-slate::input
						wire:model.defer="email"
						type="email"
						name="email"
						id="email"
						label="Your Email Address"
						placeholder="you@yourcompany.com"
						autocomplete="username"
					/>
					<x-slate::input
						wire:model.defer="password"
						id="password"
						name="password"
						type="password"
						label="Password"
						placeholder="Password (at least 8 characters)"
						autocomplete="new-password"
					/>
					<x-slot name="actions">
						<x-slate::button 
							full-width
							thick 
							wire:click="submit"
							wire:loading.attr="disabled"
							wire:loading.attr="outlined"
						>
							<span wire:loading.remove wire:target="submit">
								Register
							</span>
							<span wire:loading wire:target="submit">
								Creating Account...
							</span>
						</x-slate::button>
					</x-slot>
				</x-slate::form>
			</div>
		</div>
		<div class="w-1/4 bg-info-50 p-4">
			@include('livewire.onboarding.plan-details')
		</div>
	</div>
</div>