<div>
	<x-slate::header full-width color="white">
		<x-slot name="title">
			<div class="flex items-center space-x-2">
				<x-slate::icon icon="carbon-events" color="black" />
				<x-slate::heading tag="h1" font-bold>
					Roles
				</x-slate::heading>
			</div>
		</x-slot>
		<x-slot name="meta">
			Manage your roles and permissions
		</x-slot>
		<x-slot name="actions">
			<x-slate::button 
				rounded
				:link="route('teams.roles.create')"
			>Add Role</x-slate::button>
		</x-slot>
	</x-slate::header>


	<div class="p-6">

		@livewire('datatables.roles.roles-table')

	</div>
</div>