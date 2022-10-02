<div class="p-4">

	<div class="step-one  {{ $currentStep != 1 ? 'hidden' : '' }}">

		<div class="bg-white dark:bg-gray-900">
			<div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
				<div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
					<h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Supercharge your ecommerce marketing.</h2>
					<p class="mb-5 font- text-gray-500 sm:text-xl dark:text-gray-400">Whether you are a startup or a large ecommerce brand, we have a plan for you.</p>
				</div>
				<div class="space-y-8 md:grid md:grid-cols-3 sm:gap-6 lg:gap-10 md:space-y-0">
					@foreach (config('plans.billables.team.plans') as $plan )
						<!-- Pricing Card -->
						<div class="flex flex-col p-6 mx-auto max-w-lg text-center text-gray-900 bg-white rounded-lg border border-gray-100 shadow dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white">
							<h3 class="mb-4 text-2xl font-semibold">{{ $plan['name'] }}</h3>
							<p class="font-medium text-gray-500 sm:text-lg dark:text-gray-400">{{ $plan['short_description'] }}</p>
							<div class="flex justify-center items-baseline my-8">
								<span class="mr-2 text-5xl font-extrabold">{{ $plan['prices']['us']['monthly']['price'] }}</span>
								<span class="text-gray-500 dark:text-gray-400">/{{ $plan['prices']['us']['monthly']['interval'] }}</span>
							</div>
							<!-- List -->
							<ul role="list" class="mb-8 space-y-4 text-left">
							@foreach ($plan['features'] as $feature )
								<li class="flex items-center space-x-3">
									<!-- Icon -->
									<svg class="flex-shrink-0 w-5 h-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
									<span>{!! $feature !!}</span>
								</li>
								@endforeach
							</ul>

							@if ($team->subscribedToProduct($plan['id'], 'sprrw'))
								<x-slate::button 
									class="mt-5"
									full-width
									shadow
									thick
									color="secondary"
									size="lg"
									disabled="disabled"

								>
									CURRENT PLAN
								</x-slate::button>
							@else
								<x-slate::button 
									class="mt-5"
									full-width
									shadow
									thick
									color="primary"
									size="lg"
									wire:click="stepOneSubmit('{{$plan['id']}}')"

								>
									SELECT PLAN
								</x-slate::button>
							@endif
						</div>
					@endforeach

				</div>
			</div>
		</div>

	</div>

	<div class="step-two  {{ $currentStep != 2 ? 'hidden' : '' }}">

		@if($defaultPaymentMethod)
			<p class="text-sm font-medium">CARD ON FILE</p>
			<div class="p-2 tracking-wide font-medium bg-white rounded flex items-center justify-between">
				<span class="inline-flex items-center">
					<x-slate::icon size="sm" icon="carbon-purchase" /> {{ $defaultPaymentMethod['card']['last4']}}
				</span>
				<span class="inline-flex items-center">
					{{ $defaultPaymentMethod['card']['exp_month'] }} / {{ $defaultPaymentMethod['card']['exp_year'] }}
				</span>
			</div>
		@endif
		
		<div wire:ignore x-data="{
			clientSecret: @entangle('clientSecret')
		}"
		
		x-init="() => {

			const stripe = Stripe('{{ config('services.stripe.key') }}');
			
			@if(!$defaultPaymentMethod)
				const elements = stripe.elements();
				const cardElement = elements.create('card');
				cardElement.mount('#card-element');

				const cardHolderName = document.getElementById('card-holder-name');
			@endif
			
			const confirmButton = document.getElementById('confirm-button');

			confirmButton.addEventListener('click', (e) => {

				$wire.validateAndUpdateBillingAddress().then(result => {

					console.log(result);
					console.log('after billing check');
					
					if(result == true) {
						console.log('billing address confirmed. can proceed with card verification');
						console.log('checking if we need to update payment method');

						@if(!$defaultPaymentMethod)
							console.log('not payment method found.');
							console.log('refreshing payment intent and proceeding ahead');

							stripe.confirmCardSetup (
								clientSecret, {
									payment_method: {
										card: cardElement,
									}
								}
							).then(function(result) {

								console.log(result);
								
								if (result.error) {
									// Display 'error.message' to the user...
									$wire.handleError(result.error);
									console.log('result.error')
									console.log(result.error)
									console.log($wire)
								} else {
									// The card has been verified successfully...
									//console.log($wire);
									console.log('The card has been verified successfully...')
									$wire.updatePaymentMethod(result.setupIntent).then(() => {
										$wire.swapSubscription().then(() => {
											console.log('subscription swapped')
										});

									});
								}
								
							});	

						@else
							console.log('card is there');
							$wire.swapSubscription().then(() => {
								console.log('subscription swapped')
							});
						@endif

					} else {
						console.log('billing address not confirmed. cant proceed with card verification');

					}

				});
				
			});
		}"
		>

		@if(!$defaultPaymentMethod)
			<!-- We'll put the error messages in this element -->
			<div id="card-errors" role="alert"></div>

			<x-slate::input
					wire:ignore
					label="Card Holder Name"
					id="card-holder-name" 
					type="text" 
					autocomplete="cc-name"
					placeholder="Jason Smith"
				/>
			<label for="card-element" class="mb-1 mt-3 block font-medium text-gray-700  dark:text-gray-300">
				Card Details
			</label>
			<div 
				id="card-element"
				class="bg-white p-2.5 mb-3 flex-1 block w-full rounded-md text-gray-900 dark:text-gray-300 border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 focus:ring-primary-500 focus:border-primary-500"
			>
				<!-- Elements will create input elements here -->
			</div>
		@endif

		</div>

		<hr class="my-4"/>

		<!-- address //-->

		<div class="space-y-4">
			<x-slate::input 
				wire:model.defer="billingAddress.name"
				name="billingAddress.name"
				id="billingAddress.name"
				label="Billing Name"
			/>

			<x-slate::input 
				wire:model.defer="billingAddress.address_1"
				name="billingAddress.address_1"
				id="billingAddress.address_1"
				label="Address Line 1"
			/>

			<x-slate::input-group cols="3">
				<x-slate::input 
					wire:model.defer="billingAddress.city"
					name="billingAddress.city"
					id="billingAddress.city"
					label="City"
				/>

				<div class="space-y-0" x-data="{
					showIndianStates: @entangle('showIndianStates')
				}">

					<div x-show="showIndianStates">
						<x-slate::select 
							x-show="showIndianStates"
							wire:model="billingAddress.state"
							name="billingAddress.state"
							id="billingAddress.state"
							label="State"
						>
							@foreach ($allIndianStates as $key => $value)
								<option value="{{ $key }}">{{ $value }}</option>
							@endforeach
						</x-slate::select>

					</div>
					<div x-show="!showIndianStates">
						<x-slate::input 
							x-show="!showIndianStates"
							wire:model.defer="billingAddress.state"
							name="billingAddress.state"
							id="billingAddress.state"
							label="State"
						/>

					</div>
				</div>
				<x-slate::select 
					wire:model="billingAddress.country"
					name="billingAddress.country"
					id="billingAddress.country"
					label="Country"
					:readonly="$team->billingAddress()->exists()"
					:disabled="$team->billingAddress()->exists()"
				>
				@foreach (getCountriesArray() as $key => $value)
					<option value="{{ $key }}">{{ $value }}</option>
				@endforeach
				</x-slate::select>
			</x-slate::input-group>
			
			<x-slate::input 
				wire:model.defer="billingAddress.pincode"
				name="billingAddress.pincode"
				id="billingAddress.pincode"
				label="Pincode"
			/>

			<x-slate::button 
				id="confirm-button"
			>Confirm and Start Trial</x-slate::button>

		</div>

	</div>
	<div class="step-twoa  {{ $currentStep != 222 ? 'hidden' : '' }}">
		<x-slate::heading tag="h2">Billing Address</x-slate::heading>

			<x-slate::input 
				wire:model.defer="billingAddress.name"
				name="billingAddress.name"
				id="billingAddress.name"
				label="Billing Name"
			/>
			<x-slate::input 
				wire:model.defer="billingAddress.address_1"
				name="billingAddress.address_1"
				id="billingAddress.address_1"
				label="Address Line 1"
			/>
			<x-slate::input-group>
				<x-slate::input 
					wire:model.defer="billingAddress.city"
					name="billingAddress.city"
					id="billingAddress.city"
					label="City"
				/>
				@if($billingAddress->country == 'IN')
					<x-slate::select 
						wire:model.defer="billingAddress.state"
						name="billingAddress.state"
						id="billingAddress.state"
						label="State"
					>
						@foreach (getIndiaStateCodesArray() as $key => $value)
							<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</x-slate::select>
				@else
					<x-slate::input 
						wire:model.defer="billingAddress.state"
						name="billingAddress.state"
						id="billingAddress.state"
						label="State"
					/>
				@endif
			</x-slate::input-group>

			<x-slate::select 
				wire:model="billingAddress.country"
				name="billingAddress.country"
				id="billingAddress.country"
				label="Country"
				:readonly="$team->billingAddress()->exists()"
				:disabled="$team->billingAddress()->exists()"
			>
			@foreach (getCountriesArray() as $key => $value)
				<option value="{{ $key }}">{{ $value }}</option>
			@endforeach
			</x-slate::select>

			<x-slate::input 
				wire:model.defer="billingAddress.pincode"
				name="billingAddress.pincode"
				id="billingAddress.pincode"
				label="Pincode"
			/>

			<x-slate::button 
				class="mt-5"
				full-width
				shadow
				thick
				color="primary"
				size="lg"
				wire:click="stepTwoSubmit()"

			>
				Submit
			</x-slate::button>

	</div>

	<div class="step-three  {{ $currentStep != 3 ? 'hidden' : '' }}">
		step 3
	</div>

</div>