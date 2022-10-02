<div>
	<x-slate::header full-width color="white">
		<x-slot name="title">
			<div class="flex items-center space-x-2">
				<x-slate::icon icon="carbon-events" color="black" />
				<x-slate::heading tag="h1" font-bold>
					Invited Members
				</x-slate::heading>
			</div>
		</x-slot>
		<x-slot name="meta">
			Manage your team and teammates
		</x-slot>
		<x-slot name="actions">
			<x-slate::button 
				
				color="secondary"
				wire:click="$emit('openModal', 'modals.teams.invite')">
				Invite Teammate
			</x-slate::button>
			<x-slate::button 
				:link="route('teams.members.index')">
				All Members
			</x-slate::button>
		</x-slot>
	</x-slate::header>


	<div class="p-4">
		@livewire('datatables.teams.invited-members-table')
	</div>

</div>