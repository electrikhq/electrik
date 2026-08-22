@unless ($breadcrumbs->isEmpty())
    <x-slate::breadcrumb>
        <x-slate::breadcrumb-list class="text-sm">
            @foreach ($breadcrumbs as $breadcrumb)
                <x-slate::breadcrumb-item>
                    @if ($breadcrumb->url && ! $loop->last)
                        <x-slate::breadcrumb-link as="a" href="{{ $breadcrumb->url }}" class="max-w-[10rem] truncate sm:max-w-[14rem]" wire:navigate>
                            {{ $breadcrumb->title }}
                        </x-slate::breadcrumb-link>
                    @else
                        <x-slate::breadcrumb-page class="max-w-[10rem] truncate sm:max-w-[16rem]">{{ $breadcrumb->title }}</x-slate::breadcrumb-page>
                    @endif
                </x-slate::breadcrumb-item>

                @unless ($loop->last)
                    <x-slate::breadcrumb-separator />
                @endunless
            @endforeach
        </x-slate::breadcrumb-list>
    </x-slate::breadcrumb>
@endunless
