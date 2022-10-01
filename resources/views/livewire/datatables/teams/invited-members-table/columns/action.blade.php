<div class="inline-flex items-center">

	<x-slate::icon
		data-tippy-content="<small>Resend Invitation</small>"
		size="xs"
		icon="carbon-send-alt"
		wire:click="resendInvite({{$row->id}})"
	>
	</x-slate::icon>
	<x-slate::icon
		data-tippy-content="<small>Delete Invitation</small>"
		size="xs"
		color="danger"
		icon="carbon-close-outline"
		{{-- onclick needs to come before wire:click to avoid event propgation. ref: https://forum.laravel-livewire.com/t/confirm-an-action/260 --}}
		onclick="confirm('Are you sure you want to cancel invite?') || event.stopImmediatePropagation()"
		wire:click="deleteInvite({{$row->id}})"
	>
	</x-slate::icon>

</div>