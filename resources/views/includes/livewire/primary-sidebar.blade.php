<div class="w-20 bg-[#eff0eb] dark:bg-gray-900 flex flex-col shadow-inner justify-between overflow-y-scroll h-full">
    <div class="head space-y-12">
        <div class="px-5 mt-6 flex justify-center">
            <a href="{{ route('dashboard.index') }}">
                <span class="text-xl font-bold">{{ config('app.name', 'E') }}</span>
            </a>
        </div>
        <div class="px-5 space-y-8">
            <a href="{{ route('dashboard.index') }}" class="flex justify-center">
                <x-slate::icon 
                    data-tippy-content="<small>Dashboard</small>"
                    icon="carbon-dashboard" 
                    :color="(request()->routeIs('dashboard.*')) ? 'primary' : ''"
                    size="md" 
                />
            </a>
            <a href="{{ route('teams.index') }}" class="flex justify-center">
                <x-slate::icon 
                    data-tippy-content="<small>Teams</small>"
                    icon="carbon-user-multiple" 
                    :color="(request()->routeIs('teams.*')) ? 'primary' : ''"
                    size="md" 
                />
            </a>
            <a href="{{ route('billing.index') }}" class="flex justify-center">
                <x-slate::icon 
                    data-tippy-content="<small>Billing</small>"
                    icon="carbon-receipt" 
                    :color="(request()->routeIs('billing.*')) ? 'primary' : ''"
                    size="md" 
                />
            </a>
        </div>
    </div>
    <div class="foot mb-6">
        <div class="px-5 flex justify-center">
            <div class="settings-menu-popover-menu-wrapper">
                <div class="settings-menu-popover-menu-trigger" data-template="one">
                    <x-slate::icon 
                        onclick="return false"
                        icon="carbon-settings" 
                        :color="((request()->routeIs(['settings.*', 'account.*']))) ? 'primary' : ''"
                        size="md" 
                    />
                </div>
            </div>
        </div>
    </div>
</div>

<template id="one">
    <div class="w-80 bg-white p-2 px-6 pointer-events-auto">
        <div class="py-3">
            <strong>{{ auth()->user()->name }}</strong>
        </div>
        <hr />
        @if(auth()->user()->teams()->exists() && auth()->user()->currentTeam)
        <div class="hover:cursor-pointer settings-menu-popover-menu-trigger py-6 space-y-2 flex justify-between items-center" data-template="nested">
            <div>
                {{ auth()->user()->currentTeam->name }}<br/>
                <span class="text-sm font-medium text-neutral-500 dark:text-neutral-500">Switch Team or manage current team settings</span>
            </div>
            <div><x-slate::icon icon="carbon-chevron-right" size="xs" color="black" /></div>
        </div>
        <hr class="border-neutral-300" />
        @endif
        
        <div class="py-6 space-y-3">
            {{--<a href="{{ route('settings.profile') }}" class="flex items-center text-black hover:text-primary-600">
                <x-slate::icon color="black" icon="carbon-user" size="xs" class="mr-2" /> Your Account
            </a>--}}
            <a href="{{ route('billing.index') }}" class="flex items-center text-black hover:text-primary-600">
                <x-slate::icon color="black" icon="carbon-purchase" size="xs" class="mr-2" /> Billing
            </a>
        </div>
        <hr class="border-neutral-300" />
        <div class="py-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center text-black hover:text-primary-600">
                    Logout
                </button>
            </form>
        </div>
    </div>
</template>

<template id="nested">
    <div class="w-80 bg-white p-2 px-6 font-medium pointer-events-auto h-96 max-h-96 overflow-y-auto justify-between flex flex-col">
        <div class="team-list">
            @if(auth()->user()->teams()->exists())
                @foreach (auth()->user()->teams as $team)
                    <div class="p-2">
                        <a href="{{ route('teams.switch', $team) }}" class="text-black hover:text-primary-600">{{ $team->name }}</a>
                    </div>
                @endforeach	
            @endif
        </div>
        <div class="">
            @if(auth()->user()->currentTeam)
                <a href="{{ route('teams.settings', auth()->user()->currentTeam) }}" class="text-black hover:text-primary-600">Manage Team</a>
            @else
                <a href="{{ route('teams.index') }}" class="text-black hover:text-primary-600">Create Team</a>
            @endif
        </div>
    </div>
</template>

@push('scripts')
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let tippyInstances = [];
        
        function initializeNestedTippy(container) {
            const nestedTriggers = container.querySelectorAll(".settings-menu-popover-menu-trigger:not([data-tippy-initialized])");
            nestedTriggers.forEach(trigger => {
                trigger.setAttribute("data-tippy-initialized", "true");
                
                const instance = tippy(trigger, {
                    interactive: true,
                    placement: 'right-start',
                    trigger: "click",
                    theme: 'light',
                    allowHTML: true,
                    appendTo: document.body,
                    content(reference) {
                        const id = reference.getAttribute("data-template");
                        const template = document.getElementById(id);
                        return template ? template.innerHTML : '';
                    },
                    onShow(instance) {
                        setTimeout(() => {
                            if (instance.popper) {
                                initializeNestedTippy(instance.popper);
                            }
                        }, 100);
                    }
                });
                tippyInstances.push(instance);
            });
        }
        
        function initializeTippy() {
            tippyInstances.forEach(instance => {
                if (instance && instance.destroy) {
                    instance.destroy();
                }
            });
            tippyInstances = [];
            
            document.querySelectorAll(".settings-menu-popover-menu-trigger").forEach(trigger => {
                trigger.removeAttribute("data-tippy-initialized");
            });
            
            const triggers = document.querySelectorAll(".settings-menu-popover-menu-trigger:not([data-tippy-initialized])");
            triggers.forEach(trigger => {
                trigger.setAttribute("data-tippy-initialized", "true");
                
                const instance = tippy(trigger, {
                    interactive: true,
                    placement: 'right-start',
                    trigger: "click",
                    theme: 'light',
                    allowHTML: true,
                    appendTo: document.body,
                    content(reference) {
                        const id = reference.getAttribute("data-template");
                        const template = document.getElementById(id);
                        return template ? template.innerHTML : '';
                    },
                    onShow(instance) {
                        setTimeout(() => {
                            if (instance.popper) {
                                initializeNestedTippy(instance.popper);
                            }
                        }, 100);
                    }
                });
                tippyInstances.push(instance);
            });
        }
        
        initializeTippy();
        
        function initializeTooltips() {
            document.querySelectorAll('[data-tippy-content]').forEach(element => {
                if (!element._tippy) {
                    tippy(element, {
                        allowHTML: true,
                        placement: 'right',
                        theme: 'light',
                    });
                }
            });
        }
        
        initializeTooltips();
        
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('message.processed', () => {
                setTimeout(() => {
                    initializeTooltips();
                }, 100);
            });
        }
    });
</script>
@endpush

