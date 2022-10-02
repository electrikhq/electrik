<div class="my-48">

	<div class="flex h-full items-center w-full justify-center flex-col space-y-4">
			
		<x-slate::icon icon="carbon-user" color="info" />
		
		<x-slate::heading>No current subscription</x-slate::heading>
			
		<p>
			Start with {{ env("APP_NAME") }} today, select a plan that works for your brand.
		</p>

		<x-slate::button wire:click="$emit('openModal', 'modals.billing.subscriptions', {{ '[{ team: ' . $team->id . '}]' }})">
			Select a Plan
		</x-slate::button> 

		
	</div>
</div>