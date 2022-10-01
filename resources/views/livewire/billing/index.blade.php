<div>
<x-slate::header color="white" full-width>
		<x-slot name="title">
			<div class="flex items-center space-x-2">

				<x-slate::icon icon="carbon-receipt" color="black" />

				<x-slate::heading font-bold>{{ auth()->user()->currentTeam->name }} <span class="text-gray-">billing</span></x-slate::heading>
				
			</div>
			</x-slot>
			<x-slot name="actions">

				<!-- <x-slate::button :link="route('billing.index')">All Campaigns</x-slate::button> -->

			</x-slot>
	</x-slate::header>

	<x-slate::section transparent  rounded >
		<x-slot name="left" transparent>
			<x-slate::heading tag="h2" font-medium>Payment Methods</x-slate::heading>
			<x-slate::content>
				<p>Your payment method details we have on our file</p>
			</x-slate::content>
		</x-slot>	
		<x-slot name="right">

			@if(session()->has('alert') && session('alert')['from'] == 'updatePaymentMethod')
				<x-slate::alert color="{{ session('alert')['color'] ??= 'info' }}" dismissable>
					{{ session('alert')['message'] }}
				</x-slate::alert>
			@endif

			<x-slate::content>
				@if($team->defaultPaymentMethod())
					<p>Account will be billed to: <strong> {{ $team->defaultPaymentMethod()->card->brand }} </strong> ending with <strong>{{ $team->defaultPaymentMethod()->card->last4 }}</strong></p>
				@endif
				<x-slate::button
					wire:click="$emit('openModal', 'modals.billing.payment-method', {{ '[{ team: ' . $team->id . '}]' }})"
				>Update Payment Method
				</x-slate::button>
				<!-- <x-slate::button
					wire:click="$emit('openModal', 'modals.subscriptions', {{ '[{ team: ' . $team->id . '}]' }})"
				>Subscribe
				</x-slate::button> -->
			</x-slate::content>

		</x-slot>
	</x-slate::section>
	
	@if($team->billingAddress()->exists() && $team->billingAddress()->first()->country == 'IN')
	<x-slate::section transparent>
		<x-slot name="left" transparent>
			<x-slate::heading tag="h2" font-medium>Billing GSTIN</x-slate::heading>
			<x-slate::content>
				<p>If you are a business based in India and want to avail GST, please provide your GST number here.</p>
			</x-slate::content>
		</x-slot>	
		<x-slot name="right">

			@if(session()->has('alert') && session('alert')['from'] == 'updateTaxId')
				<x-slate::alert color="{{ session('alert')['color'] }}" dismissable>
					{{ session('alert')['message'] }}
				</x-slate::alert>
			@endif

			
			
			<x-slate::form wire:submit.prevent="updateTaxId" full-width>

				<x-slate::input 
					wire:model="in_gst"
					name="in_gst"
					id="in_gst"
					label="GSTIN"
				/>

				<x-slot name="actions">
					<x-slate::button 
						wire:click.prevent="updateTaxId" 
						class="secondary"
						wire:loading.attr="disabled"
						wire:loading.attr="outlined"
					>
						<span wire:loading.remove wire:target="updateTaxId">
							Save
						</span>
						<span wire:loading wire:target="updateTaxId">
							Updating...
						</span>
					</x-slate::button>
				</x-slot>

			</x-slate::form>
		</x-slot>
	</x-slate::section>
	@endif

	<x-slate::section transparent>
		<x-slot name="left" transparent>
			<x-slate::heading tag="h2" font-medium>Billing Address</x-slate::heading>
			<x-slate::content>
				<p>Update your billing address details</p>
			</x-slate::content>
		</x-slot>	
		<x-slot name="right">

			@if(session()->has('alert') && session('alert')['from'] == 'updateBillingAddress')
				<x-slate::alert color="{{ session('alert')['color'] }}" dismissable>
					{{ session('alert')['message'] }}
				</x-slate::alert>
			@endif

			<x-slate::form wire:submit.prevent="updateBillingAddress" full-width>

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

				<x-slate::input 
					wire:model.defer="billingAddress.address_2"
					name="billingAddress.address_2"
					id="billingAddress.address_2"
					label="Address Line 2"
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
				
				<x-slot name="actions">
					<x-slate::button 
						wire:click.prevent="updateBillingAddress" 
						class="secondary"
						wire:loading.attr="disabled"
						wire:loading.attr="outlined"
					>
						<span wire:loading.remove wire:target="updateBillingAddress">
							Save
						</span>
						<span wire:loading wire:target="updateBillingAddress">
							Updating...
						</span>
					</x-slate::button>
				</x-slot>
			</x-slate::form>
		</x-slot>
	</x-slate::section>
</div>