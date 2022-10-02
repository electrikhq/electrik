<div
	class="absolute mx-auto bottom-4 space-y-4 left-1/2 transform -translate-x-1/2 max-h-48 overflow-y-hidden z-50"
    x-data='ToastComponent($wire)'
    @mouseleave="scheduleRemovalWithOlder()"
>
    <template
        x-for="toast in toasts.filter((a)=>a)" :key="toast.index"
    >
        <div
            @mouseenter="cancelRemovalWithNewer(toast.index)"
            @mouseleave="scheduleRemovalWithOlder(toast.index)"
            @click="remove(toast.index)"
            x-show="toast.show===1"

            x-init="$nextTick(()=>{toast.show=1})"
        >
            @include('tall-toasts::includes.content')
        </div>
    </template>
</div>
