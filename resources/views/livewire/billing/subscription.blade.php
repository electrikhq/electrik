<div>
	<x-slate::header color="white" full-width>
		<x-slot name="title">
			<div class="flex items-center space-x-2">

				<x-slate::icon icon="carbon-receipt" color="black" />

				<x-slate::heading font-bold>{{ auth()->user()->currentTeam->name }} <span class="text-gray-">subscriptions</span></x-slate::heading>
				
			</div>
			</x-slot>
			<x-slot name="actions">

			<x-slate::button
				wire:click="$emit('openModal', 'modals.billing.subscriptions', {{ '[{ team: ' . $team->id . '}]' }})"
			>
				Change Subscriptions
			</x-slate::button>

			</x-slot>
	</x-slate::header>

	<div class="bg- p-6">
		
		<div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
			@foreach($subscriptions as $subscription)
				<div class="border rounded-md shadow-xs bg-white border-gray-400/50 p-6">
					<x-slate::heading tag="h2">
						{{ collect(config('plans.billables.team.plans'))->where('id', $subscription->items()->first()->stripe_product)->first()['name'] }}
					</x-slate::heading>
					<div class="my-2">
						<x-slate::badge>{{ $subscription->stripe_status }}</x-slate::badge>
					</div>
					<p class="font-medium">{{ collect(config('plans.billables.team.plans'))->where('id', $subscription->items()->first()->stripe_product)->first()['short_description'] }}</p>
					@if($subscription->hasInCompletePayment())
						<x-slate::button
							class="mt-4"
							:link="route('cashier.payment', [$subscription->latestPayment()->id, 'redirect' => route('billing.subscription')] ) "
						>Complete Payment</x-slate::button>
					@endif
					@if($subscription->ends_at)
						<p class="mt-4">
							{{ 'Expires on' . $subscription->ends_at->format('d M Y') }}
						</p>
					@endif

					<p class="mt-4">
						<x-slate::link 
							wire:click="cancelSubscription('{{ $subscription->stripe_id }}')"
						>Cancel this subcription</x-slate::link>
					</p>
				</div>

			@endforeach

		</div>
	</div>
</div>