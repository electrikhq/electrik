<div 
	x-data="{
		hasSubMenu: {!! (request()->routeIs([ 'dashbboard.*','settings.*', 'billing.*', 'teams.*'])) ? 'true' : 'false' !!},
	}"
	class="flex w-20 min-w-[5rem]"
	x-bind:class="{
		'w-80 min-w-[20rem]': hasSubMenu,
		'w-20 min-w-[5rem]' : !hasSubMenu
	}"
>

	<div class="w-20 bg-gray-300 dark:bg-gray-900 flex flex-col shadow-inner justify-between overflow-y-scroll">
		<div class="head space-y-12">
			<div class="px-5 mt-6">
				<x-slate::icon icon="carbon-recording-filled" color="" size="md" />
			</div>
			<div class="px-5">
				<x-slate::icon 
					data-tippy-content="<small>Dashboard</small>"
					icon="carbon-dashboard" 
					:color="(request()->routeIs('dashboard.*')) ? 'primary' : ''"
					size="md" 
					:link="route('dashboard.index')" 
				/>
			</div>
		</div>
		<div class="foot mb-6">
			<div class="px-5">
				<div class="settings-menu-popover-menu-wrapper">
					<div class="settings-menu-popover-menu-trigger" data-template="one">
						<x-slate::icon 
							onclick="return false"
							icon="carbon-settings" 
							:color="((request()->routeIs(['settings.*', 'billing.*', 'teams.*']))) ? 'primary' : ''"
							size="md" 
							type="link" 
							:link="route('settings.personal')" 
						/>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="w-60 relative h-full"
		x-bind:class="{
			'hidden': !hasSubMenu,
		}"
	>
	

		<div class="px-0" 
			x-data="{ 
				active: {!! (request()->routeIs(['settings.*', 'billing.*', 'teams.*'])) ? 'true' : 'false' !!},
			}"
			x-bind:class="{
				'flex flex-row': active,
			}">

			<div x-bind:class="{hidden: !active, 'flex': active}" class="hidden flex-col absolute overflow-y-auto w-60 bg-gray-100 h-full shadow-inner">
				<div class="flex flex-col py-4 px-4 space-y-0">
					<x-slate::heading tag="h2" font-bold class="my-4">Settings</x-slate::heading>
					<x-slate::sidebar-dropdown title="General" title-bold :active="request()->routeIs('settings.*')">
						<x-slate::sidebar-item :link="route('settings.personal')">
							Your account
						</x-slate::sidebar-item>
						<x-slate::sidebar-item :link="route('settings.2fa')">
							Two Factor Auth
						</x-slate::sidebar-item>
						
					</x-slate::sidebar-dropdown>
					
					<x-slate::sidebar-dropdown title="Billing" title-bold :active="request()->routeIs('billing.*')" >
							<x-slate::sidebar-item :link="route('billing.index')">
								General
							</x-slate::sidebar-item>
							<x-slate::sidebar-item :link="route('billing.invoices')">
								Invoices
							</x-slate::sidebar-item>
							<x-slate::sidebar-item :link="route('billing.subscription')">
								Subscriptions
							</x-slate::sidebar-item>
						</x-slate::sidebar-dropdown>

					<x-slate::sidebar-dropdown title="Team" title-bold :active="request()->routeIs('teams.*')">
						<x-slate::sidebar-item :link="route('teams.settings')" :active="request()->routeIs('teams.settings')">
							General Settings
						</x-slate::sidebar-item>

						<x-slate::sidebar-item :link="route('teams.members.index')">
							Team members
						</x-slate::sidebar-item>
						<x-slate::sidebar-item :link="route('teams.members.invited')">
							Invited team members
						</x-slate::sidebar-item>
						<x-slate::sidebar-item :link="route('teams.roles.index')">
							Team roles
						</x-slate::sidebar-item>
					</x-slate::sidebar-dropdown>

					
				</div>
			</div>

		</div>

	</div>

</div>

<template id="one">
	<div class="w-80 bg-white p-2 px-6 font-medium pointer-events-auto">
		<div class="py-3">
			<strong>{{ auth()->user()->name }}</strong>
		</div>
		<hr />
		@if(auth()->user()->teams()->exists())
		<div class="hover:cursor-pointer settings-menu-popover-menu-trigger py-6 space-y-2 font-semibold flex justify-between items-center" data-template="nested">
					
			<div>
				{{ auth()->user()->currentTeam->name }}<br/>
				<span class="text-sm font-medium text-gray-500">Switch Team or manage current team settings</span>
			</div>
			<div><x-slate::icon icon="carbon-chevron-right" size="xs" color="black" /></div>

		</div>
		<hr />
		@endif
			
		<div class="py-6 space-y-2 font-semibold">
			<x-slate::link color="black" :href="route('teams.settings')" class="flex items-center">
				<x-slate::icon color="black" icon="carbon-settings" size="xs" /> Settings
			</x-slate::link>
			<x-slate::link color="black" :href="route('settings.personal')" class="flex items-center">
				<x-slate::icon color="black" icon="carbon-user" size="xs" /> Your account
			</x-slate::link>
			<x-slate::link color="black" :href="route('billing.index')" class="flex items-center">
				<x-slate::icon color="black" icon="carbon-purchase" size="xs" /> Billing
			</x-slate::link>
		</div>
		<hr />
		<div class="py-6 font-semibold">
			<x-slate::link href="mailto:support@uneasydata.com" color="black" class="flex items-center">
				<x-slate::icon icon="carbon-email" color="black"  size="xs" /> Support
			</x-slate::link>
		</div>
		<hr />
		<div class="py-6 font-semibold">
			<x-slate::link :href="route('logout')" class="flex items-center">
				<x-slate::icon icon="carbon-logout" size="xs" /> Logout
			</x-slate::link>
		</div>

	
	</div>

</template>

<template id="nested">
	<div class="w-80 bg-white p-2 px-6 font-medium pointer-events-auto h-96 max-h-96 overflow-y-auto justify-between flex flex-col">

	<div class="team-list">
		@if(auth()->user()->teams()->exists())

			@foreach (auth()->user()->teams as $team)
				<div class="p-2">
					<x-slate::link :href="route('teams.switch', $team)">{{ $team->name }}</x-slate::link>
				</div>
			@endforeach	

		@endif
	</div>
	<div class="">
		<x-slate::link :href="route('teams.settings')">Manage Team</x-slate::link>
	</div>
	</div>
</template>

@push('scripts')
<script>
	
	document.addEventListener("DOMContentLoaded", function() {

		
		const observer = new MutationObserver(function (mutations) {
			mutations.forEach(function (mutation) {
				if (mutation.addedNodes.length) {
					initializeTippy();
				}
			});
		});
		observer.observe(
			document.querySelector(".settings-menu-popover-menu-wrapper"),
			{
				childList: true
			}
		);

		initializeTippy();
		function initializeTippy() {
			return tippy(".settings-menu-popover-menu-trigger", {
				interactive: true,
				placement: 'right-start',
				trigger: "click",
				theme: 'light',
				allowHTML: true,
				appendTo: "parent",
				content(reference) {
					const id = reference.getAttribute("data-template");
					const template = document.getElementById(id);
					return template.innerHTML;
				}
			});
		}

	});
	
</script>
@endpush