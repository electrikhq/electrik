<div class="flex flex-col items-start w-full px-6 py-6 h-full">
    <h2 class="text-xl font-medium items-center flex space-x-4">Settings</h2>
    
    @php
        $currentRouteName = Route::currentRouteName();
    @endphp

    <div class="mt-6 w-full">
        <h3 class="text-sm font-semibold text-neutral-500 dark:text-neutral-400 uppercase mb-3">Account</h3>
        <a class="hover:underline block mt-2 {{ $currentRouteName === 'settings.profile' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('settings.profile') }}">Profile</a>
        <a class="hover:underline block mt-3 {{ $currentRouteName === 'settings.security' ? 'underline text-primary-600 dark:text-primary-700' : '' }}" href="{{ route('settings.security') }}">Security</a>
    </div>
</div>
