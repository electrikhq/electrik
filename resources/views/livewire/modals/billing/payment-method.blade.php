<div>
	<div
		x-data="{}"
		x-init="() => {
			const stripe = Stripe('{{ env('STRIPE_KEY') }}');

			const elements = stripe.elements();

			const cardElement = elements.create('card');

			cardElement.mount('#card-element');

			const cardHolderName = document.getElementById('card-holder-name');
			
			const cardButton = document.getElementById('card-button');

			cardButton.addEventListener('click', async (e) => {

				@this.refreshSetupIntent();

				const { setupIntent, error } = await stripe.confirmCardSetup (
					'{{ $clientSecret }}', {
						payment_method: {
							card: cardElement,
							billing_details: { name: cardHolderName.value }
						}
					}
				);

				if (error) {
					// Display 'error.message' to the user...
					@this.handleError(error);
					console.log(error)
					console.log(@this)
				} else {
					// The card has been verified successfully...
					console.log(@this);
					console.log('success')
					@this.updateDefaultPaymentMethod(setupIntent);
				}
			});


		}"
		class="p-6 space-y-4"
	>

		<x-slate::heading tag="h1">
			Update Payment Method
		</x-slate::heading>
			
		<x-slate::input
			wire:ignore
			label="Card Holder Name"
			id="card-holder-name" 
			type="text" 
		/>

		<!-- Stripe Elements Placeholder -->
		<div>
			<label for="card-element" class="block font-medium text-gray-700  dark:text-gray-300">
				Card Details
			</label>
			<div 
				wire:ignore 
				id="card-element" 
				class="p-2.5 flex-1 block w-full rounded-md text-gray-900 dark:text-gray-300 border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white focus:ring-primary-500 focus:border-primary-500"
			></div>
		</div>

		<hr />
		<x-slate::button 
			id="card-button" 
			wire:loading.class="disabled"
			wire:loading.attr="disabled"
		>
			
			<span wire:loading.remove>
				Update Payment Method
			</span>
			<span wire:loading.block>
				Please wait...
			</span>
		</x-slate::button>
		<x-slate::button 
			id="" 
			wire:loading.class="disabled"
			wire:loading.attr="disabled"
			color="danger"
			wire:click="cancel"
		>
			Cancel
		</x-slate::button>

	</div>
</div>