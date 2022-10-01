<div class="p-0">

	<x-slate::card>
		<x-slot name="header">
			<x-slate::heading tag="h3">
				Invite Team Member
			</x-slate::heading>
		</x-slot>
		<x-slot name="content">
			<x-slate::input
				wire:model.defer="invite.email"
				name="invite.email"
				label="Email Address"
				id="invite.email"
				type="text" 
			/>
			<x-slate::select
				wire:model.defer="roleId"
				name="roleId"
				id="roleId"
				label="Role"
			>
			<option>Select Role</option>
			@foreach($allRoles as $role) 
				<option value="{{ $role->id }}">{{ $role->name }}</option>
			@endforeach
			</x-slate::select>
		</x-slot>

		<x-slot name="actions" align="right">
			<x-slate::button
				wire:click="invite"
				wire:loading.attr="disabled"
			>
			Invite
			</x-slate::button>
		</x-slot>
	</x-slate::card>    

</div>
