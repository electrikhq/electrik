<div class="">
<x-slate::header full-width trasparent>
	<x-slot name="title">
		<x-slate::content>
			<x-slate::heading tag="h1" font-bold>Edit teammate {{ $user->name . '(' . $user->email . ')' }}</x-slate::heading>
		</x-slate::content>
	</x-slot>
	<x-slot name="meta">
		
	</x-slot>

</x-slate::header>

<x-slate::section cols="1" transparent>


    <x-slate::form wire:submit.prevent="create">
		<x-slate::select
				wire:model.defer="selectedRoleId"
				name="selectedRoleId"
				id="selectedRoleId"
				label="Role"
				placeholder="Select Role"
			>
			<option value="">Select Role</option>
			@foreach($allRoles as $role) 
				<option 
					@if($selectedRoleId == $role->id) 
						{{ 'selected="selected"' }} 
					@endif 
				value="{{ $role->id }}">{{ $role->name }}</option>
			@endforeach
		</x-slate::select>
		<x-slot name="actions">
			<x-slate::button 
				wire:click.prevent="update"
				color="primary"
				wire:loading.attr="disabled"
			>
				Update
			</x-slate::button>
		</x-slot>
	</x-slate::form>
</x-slate::section>
</div>