<div>
	<x-slate::header full-width color="white">
		<x-slot name="title">
			<div class="flex items-center space-x-2">
				<x-slate::icon icon="carbon-events" color="black" />
				<x-slate::heading tag="h1" font-medium>
					Teams
				</x-slate::heading>
			</div>
		</x-slot>
		<x-slot name="meta">
			Manage your team and teammates
		</x-slot>
	</x-slate::header>


	<div class="p-6">
		<x-slate::form wire:submit.prevent="update">
			<x-slate::input 
				wire:model.defer="team.name"
				id="team.name"
				name="team.name"
				label="Team Name"
				help-text="Name of your team"
			/>
			<x-slot name="actions">
				<x-slate::button 
					wire:click.prevent="update" 
					wire:loading.attr="disabled"
					wire:loading.attr="outlined"
				>
					<span wire:loading wire:target="update">
						<div  class="items-center flex">
							<x-slate::icon class="animate-spin" color="white" size="xs" icon="carbon-progress-bar-round" /> Updating...
						</div>
					</span>
					<span wire:loading.remove wire:target="update">
						Update
					</span>
				</x-slate::button>
			</x-slot>
		</x-slate::form>
	</div>

</div>