<div>
	<x-slate::header full-width color="white">
		<x-slot name="title">
			<div class="flex items-center space-x-2">
				<x-slate::icon icon="carbon-user-settings" color="black" />
				<x-slate::heading tag="h1" font-bold>
					Your Account
				</x-slate::heading>
			</div>
		</x-slot>
		<x-slot name="meta">

		</x-slot>
	</x-slate::header>

	<x-slate::section cols="1" class="max-w-2xl ">

		<x-slate::heading tag="h2">Profile Information</x-slate::heading>
		<x-slate::content>
			<p>Update your account's profile information and email address.</p>
		</x-slate::content>


		<x-slate::form wire:submit.prevent="submit" full-width class="mt-6">
			<x-slate::input label="Full Name" helpText="Enter you full name" placeholder="John Smith" wire:model="user.name" />

			<x-slate::input label="Email Address" helpText="Your Email Address" :value="$user->email" disabled="disabled" />

			<x-slate::select wire:model="user.timezone" id="user.timezone" name="user.timezone" label="Timezone" help-text="Select your timezone.">
				@foreach (timezones() as $key => $value)
				<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
			</x-slate::select>

			<x-slot name="actions">
				<x-slate::button wire:click.prevent="submit" class="secondary" wire:loading.attr="disabled" wire:loading.attr="outlined">
					<span wire:loading.remove wire:target="submit">
						Update
					</span>
					<span wire:loading wire:target="submit">
						Updating...
					</span>
				</x-slate::button>

			</x-slot>
		</x-slate::form>
	</x-slate::section>
</div>