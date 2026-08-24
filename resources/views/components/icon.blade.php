@props([
    'name',
    'class' => 'size-4',
])

@php
    $attributes = $attributes->class($class);
@endphp

@switch($name)
    @case('dashboard')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5 12 4l9 9.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 11.5V20h13v-8.5" />
        </svg>
        @break

    @case('enterprise')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20V6l8-3 8 3v14" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20v-4h6v4M8 9h.01M12 9h.01M16 9h.01M8 12h.01M12 12h.01M16 12h.01" />
        </svg>
        @break

    @case('wallet')
    @case('purchase')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.5A2.5 2.5 0 0 1 5.5 6H18a3 3 0 0 1 3 3v7a2 2 0 0 1-2 2H5.5A2.5 2.5 0 0 1 3 15.5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9h15.5A2.5 2.5 0 0 0 16 6.5H5.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 13h3" />
        </svg>
        @break

    @case('user')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0 1 14 0" />
        </svg>
        @break

    @case('notification')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 16h12l-1.2-1.8a4 4 0 0 1-.8-2.2V10a4 4 0 1 0-8 0v2c0 .8-.28 1.58-.8 2.23L6 16Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 18a2 2 0 0 0 4 0" />
        </svg>
        @break

    @case('user-multiple')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19a4.5 4.5 0 0 1 9 0" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11a2.5 2.5 0 1 0 0-5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 0 1 5 0" />
        </svg>
        @break

    @case('user-role')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0 1 10.5-6" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m16 17 1.5 1.5L21 15" />
        </svg>
        @break

    @case('settings')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.25a2.75 2.75 0 1 0 0 5.5 2.75 2.75 0 0 0 0-5.5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m19 12 .94 1.63-1.88 3.25-1.88-.43a6.96 6.96 0 0 1-1.42.82L13.5 20h-3l-.26-1.73a6.96 6.96 0 0 1-1.42-.82l-1.88.43-1.88-3.25L5 12l-.94-1.63 1.88-3.25 1.88.43c.44-.34.91-.61 1.42-.82L10.5 4h3l.26 1.73c.5.21.98.48 1.42.82l1.88-.43 1.88 3.25Z" />
        </svg>
        @break

    @case('report')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 20h12" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16v-5M12 16V8M16 16v-2" />
        </svg>
        @break

    @case('catalog')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 5h9a3 3 0 0 1 3 3v11H9a3 3 0 0 0-3 3Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 5a3 3 0 0 0-3 3v11a3 3 0 0 1 3-3h12" />
        </svg>
        @break

    @case('renew')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 6v5h-5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 11a8 8 0 1 0 2 5.25" />
        </svg>
        @break

    @case('location')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
        </svg>
        @break

    @case('receipt')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10v16l-2-1.5L13 20l-1-1.5L11 20l-2-1.5L7 20Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 9h5M9.5 12h5M9.5 15h3" />
        </svg>
        @break

    @case('password')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="4" y="11" width="16" height="9" rx="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V8a4 4 0 1 1 8 0v3" />
        </svg>
        @break

    @case('devices')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="3" y="5" width="13" height="10" rx="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 19h3M19 9h2v10h-8v-2" />
        </svg>
        @break

    @case('connect')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 14.5 14.5 9.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 17H6a3 3 0 0 1 0-6h2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7h2a3 3 0 1 1 0 6h-2" />
        </svg>
        @break

    @default
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="12" r="8" />
        </svg>
@endswitch
