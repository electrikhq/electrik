<div class="">
<x-slate::header full-width trasparent>
	<x-slot name="heading">
		<x-slate::content>
			<x-slate::heading tag="h2">Create a new team</x-slate::heading>
		</x-slate::content>
	</x-slot>
	<x-slot name="meta">
		
	</x-slot>

</x-slate::header>

<x-slate::section cols="1" transparent>

    <x-slate::form wire:submit.prevent="create">
		<x-slate::input 
			name="name" 
			wire:model.defer="name"
			label="Team name"
			placeholder="My Awesome Team"
			help-text="Give your team a unique name"
		></x-slate::input>
		<x-slot name="actions">
			<x-slate::button 
				@click.prevent="create"
				color="primary"
				wire:loading.attr="disabled"
			>
				<span wire:loading.remove>
					Create
				</span>
				<span wire:loading>
					Creating
				</span>
			</x-slate::button>
		</x-slot>
	</x-slate::form>
</x-slate::section>
</div>