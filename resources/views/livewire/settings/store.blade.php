<div>
	<x-slate::header full-width transparent >
		<x-slot name="title">
			<div class="text-2xl text-gray-800 font-bold leading-7 sm:truncate">
				{{ __('account.settings.page.title') }}
			</div>
			<x-slot name="meta">
				{{ __('account.settings.page.description') }}
			</x-slot>
			<x-slot name="actions">
				<x-slate::button>
					aksdjlkajd alkdjlaskdjal skdj 
				</x-slate::button>
			</x-slot>
		</x-slot>
	</x-slate::header>

	<x-slate::space size="xs" />

	<x-slate::section transparent class="max-w-7xl mx-auto">
		<x-slot name="left" transparent>
			<x-slate::heading tag="h2" font-medium>Your Store</x-slate::heading>
			<x-slate::content>
				<p>Details of your current store</p>
			</x-slate::content>
		</x-slot>	
		<x-slot name="right" transparent>
			@if(session()->has('alert') && isset(session('alert')['from']) && session('alert')['from'] == 'update')
				<x-slate::alert color="{{ session('alert')['color'] }}" dismissable>
					{{ session('alert')['message'] }}
				</x-slate::alert>
			@endif
			<x-slate::form wire:submit.prevent="update" full-width disabled>
				<x-slate::input 
					wire:model="store.domain"
					name="store.domain"
					id="store.domain"
					label="Store Domain"
					disabled="disabled"
				/>
				<x-slate::input 
					wire:model="store.name"
					name="store.name"
					id="store.name"
					label="Store Name"
					helpText="Enter your store's name. This will be used in all communications to your customers"
					placeholder="Awesome Caps"
				/>
				<x-slate::select 
					wire:model="store.currency"
					name="store.currency"
					id="store.currency"
					label="Store Currency"
					helpText="Enter your store's currency"
					placeholder="Selct Currency"
				>
				@foreach (getCurrenciesArray() as $key => $value)
					<option value="{{ $key }}">{{ ucwords($value) }}</option>
				@endforeach
				</x-slate::select>
				<x-slot name="actions">
					<x-slate::button 
						wire:click.prevent="update" 
						class="secondary"
						wire:loading.attr="disabled"
						wire:loading.attr="outlined"
					>
						<span wire:loading.remove wire:target="update">
							Update
						</span>
						<span wire:loading wire:target="update">
							Updating...
						</span>
					</x-slate::button>
				</x-slot>
			</x-slate::form>
		</x-slot>
	</x-slate::section>
</div>