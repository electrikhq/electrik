@props([
    'user' => null,
])

@php
    use Electrik\Models\Announcement;

    $user ??= auth()->user();
    $announcement = ($user && \Illuminate\Support\Facades\Schema::hasTable('announcements'))
        ? Announcement::activeFor($user)
        : null;
@endphp

@if ($announcement)
    <div
        data-slot="announcement-banner"
        class="border-b border-border bg-muted/60 px-4 py-2.5 text-foreground"
        role="status"
    >
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="text-sm font-medium">{{ $announcement->title }}</p>
                <p class="text-sm text-muted-foreground">{{ $announcement->body }}</p>
            </div>
        </div>
    </div>
@endif
