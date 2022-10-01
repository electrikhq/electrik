<x-slate::banner

x-bind:class="{
                    'bg-info-600': toast.type === 'info',
                    'bg-success-600': toast.type === 'success',
                    'bg-warning-600': toast.type === 'warning',
                    'bg-danger-600': toast.type === 'danger'
                  }"

dismissable
slim
rounded 
shadow
class="rounded-md"
>

<div
                x-show="toast.message !== undefined"
                x-html="toast.message"
            ></div>
</x-slate::banner>
