<div class="inline-flex items-center">

	<!-- <x-slate::icon
		data-tippy-content="<small>Remove User</small>"
		size="xs"
		color="danger"
		icon="carbon-close-outline"
		wire:click="detachFromTeam({{$row->id}})"
	>
	</x-slate::icon> -->

	<x-slate::icon 
		icon="carbon-close-outline" 
		data-tippy-content="<small>Remove User</small>"
		size="xs" 
		color="danger"
		{{-- onclick needs to come before wire:click to avoid event propgation. ref: https://forum.laravel-livewire.com/t/confirm-an-action/260 --}}
		onclick="confirm('Are you sure you want to remove this user?') || event.stopImmediatePropagation()"
		wire:click="detachFromTeam({{$row->id}})"
	></x-slate::icon>


</div>