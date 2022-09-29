<div class="flex flex-col h-screen w-full">
<script src="//js.stripe.com/v3/"></script>

	<div class="">
		<div class="flex space-x-12 py-6 pl-6 border-b border-gray-200">
			<a class="text-lg font-bold" href="#" onlick="return false;" aria-current="page">{{ config('app.name') }}</a>

			<a class="text-lg font-medium text-gray-600" href="#" onclick="return false;">1. Choose Plan</a>
			
			<a class="text-lg font-medium text-gray-600" href="#" onclick="return false;">2. Create Account</a>

			<a class="text-lg font-bold text-primary-600" href="#" onclick="return false;">3. Confrim Details</a>
		</div>
	</div>
	<div class="flex h-full">
		<div class="flex-1 flex bg--50 items-center">
			<div class="flex-1 xs:mx-12 sm:mx-24 md:mx-24 max-w-3xl mx-auto">

				@if($selectedPlan['slug'] != 'sprrw.core' )
					<x-slate::content>
						<x-slate::heading tag="h1">
							Start your free trial.
						</x-slate::heading>

						
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
							<div
								x-data="{
									clientSecret: @entangle('clientSecret')
								}"
								
								x-init="() => {
									
									@if(!$defaultPaymentMethod)
									
										const stripe = Stripe('{{ config('services.stripe.key') }}');
										const elements = stripe.elements();
										const cardElement = elements.create('card');
										cardElement.mount('#card-element');
										const cardHolderName = document.getElementById('card-holder-name');
									
									@endif
									
									const cardButton = document.getElementById('confirm-button');

									cardButton.addEventListener('click', (e) => {

										@if(!$team->billingAddress()->exists())

											console.log('checking billing address');
											$wire.validateBillingAddress().then(result => {

												console.log(result);
												console.log('after billing check');

												if(result == true) {
													@if(!$defaultPaymentMethod)

														$wire.refreshSetupIntent().then(function() {
															
															console.log('refreshed intent')
															console.log('sending stripe')
															console.log(clientSecret);
															
															stripe.confirmCardSetup (
																clientSecret, {
																	payment_method: {
																		card: cardElement,
																		//billing_details: { name: cardHolderName.value }
																	}
																}
															).then(function(result) {

																console.log(result);
																
																if (result.error) {
																	// Display 'error.message' to the user...
																	$wire.handleError(result.error);
																	console.log(result.error)
																	console.log($wire)
																} else {
																	// The card has been verified successfully...
																	console.log($wire);
																	console.log('// The card has been verified successfully...')
																	$wire.completeSetup(result.setupIntent);
																}
																
															});	

														});
													@else
														$wire.completeSetup();
													@endif
												}

											});

										@elseif(!$defaultPaymentMethod)

											$wire.refreshSetupIntent().then(function() {
													
													console.log('refreshed intent')
													console.log('sending stripe')
													console.log(clientSecret);
													
													stripe.confirmCardSetup (
														clientSecret, {
															payment_method: {
																card: cardElement,
																billing_details: { name: cardHolderName.value }
															}
														}
													).then(function(result) {

														console.log(result);
														
														if (result.error) {
															// Display 'error.message' to the user...
															$wire.handleError(result.error);
															console.log(result.error)
															console.log($wire)
														} else {
															// The card has been verified successfully...
															console.log($wire);
															console.log('// The card has been verified successfully...')
															$wire.completeSetup(result.setupIntent);
														}
														
													});	

												});

										@else
											console.log('swapping subscription');
											$wire.swapSubscription();
										@endif

									});


								}"
								class="space-y-4"
							>
							@if(!$defaultPaymentMethod)
									
								<x-slate::input
									wire:ignore
									label="Card Holder Name"
									id="card-holder-name" 
									type="text" 
									autocomplete="cc-name"
								/>

								<!-- Stripe Elements Placeholder -->
								<div>
									<label for="card-element" class="block font-medium text-gray-700  dark:text-gray-300">
										Card Details
									</label>
									<div 
										wire:ignore 
										id="card-element" 
										class="bg-white p-2.5 flex-1 block w-full rounded-md text-gray-900 dark:text-gray-300 border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white focus:ring-primary-500 focus:border-primary-500"
									></div>
								</div>
								@endif

								@if(!$team->billingAddress()->exists())

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

								@endif
							</div>

						<div class="py-6">
							<x-slate::button 
								id="confirm-button"
								class="secondary"
								wire:loading.attr="disabled"
								wire:loading.attr="outlined"
								full-width
								:disabled="!$selectedPlan"
								wire:loading.attr="disabled"
							>
								<span wire:loading.remove>
									{{ $selectedPlan ? 'CONFIRM' : 'SELECT A PLAN' }}
								</span>
								<span wire:loading>
									...
								</span>
							</x-slate::button>
						</div>
					</x-slate::content>
				@else

					<x-slate::heading tag="h1">
						All Set.
					</x-slate::heading>
					<p class="mb-2 text-lg text-gray-600">You are all set! Welcome to {{ env('APP_NAME') }}.</p>
					<x-slate::button
						:link="route('dashboard.index')"
					>
					View Dashboard
					</x-slate::button>
				@endif
			</div>
		</div>
		<div class="w-1/4 bg-info-50 p-4">
			@include('electrik::livewire.onboarding.plan-details')
		</div>
	</div>
</div>

@push('scripts')

@endpush