@unless ($breadcrumbs->isEmpty())
    <nav class="my-2 mb-4">
        <ol class="flex flex-wrap">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <li>
                        <a href="{{ $breadcrumb->url }}" class="text-primary-600 hover:text-primary-900 hover:underline focus:text-primary-900 focus:underline">
                            {{ $breadcrumb->title }}
                        </a>
                    </li>
                @else
                <li class="text-neutral-500 dark:text-neutral-500 px-2">
                        {{ $breadcrumb->title }}
                    </li>
                @endif

                @unless($loop->last)
                    <li class="text-neutral-500 dark:text-neutral-500 px-2">
                        /
                    </li>
                @endif

            @endforeach
        </ol>
    </nav>
@endunless

