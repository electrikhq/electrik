<div>
	<x-slate::header full-width color="white">
		<x-slot name="title">
			<div class="flex items-center space-x-2">
				<x-slate::icon icon="carbon-user-settings" color="black" />
				<x-slate::heading tag="h1" font-bold>
					Email Addresses for <span class="text-stone-500">{{ auth()->user()->currentTeam->name }}</span>
				</x-slate::heading>
			</div>
		</x-slot>
		<x-slot name="meta">
			Manage custom email addresses
		</x-slot>
	</x-slate::header>

	<x-slate::section cols="1" class="max-w-2xl ">

		<x-slate::form wire:submit.prevent="submit">
			<x-slate::heading tag="h2" font-bold>From Email Name</x-slate::heading>
			<x-slate::input
				wire:model.defer="fromEmailName"
				id="fromEmailName"
				name="fromEmailName"
				label="From Email Name"
			/>
			<x-slate::heading tag="h2" font-bold>From Email Address</x-slate::heading>
			<x-slate::input-group>

				<x-slate::input
					wire:model.defer="username"
					id="username"
					name="username"
					label="Username"
					help-text="All outgoing emails and notifications from {{ config('app.name') }} will use this as from address."
				
				/>
				<x-slate::select
					wire:model.defer="domain"
					addon="@"
					addonPosition="before"
					id="domain"
					name="domain"
					label="Domain"
				>
					@foreach($domains as $domain) 
						<option value="{{$domain->id}}">{{ $domain->name }}</option>
					@endforeach
				</x-slate::select>

			</x-slate::input-group>
			

			<x-slate::button
				type="submit"
				wire:click.prevent="submit"
			>
			Save
			</x-slate::button>
		</x-slate::form>
	</x-slate::section>
</div>