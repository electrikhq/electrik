<?php

namespace Electrik\Livewire\Settings;

use Electrik\Actions\Teams\DeleteTeam;
use Electrik\Support\Locales;
use Electrik\Support\Timezones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\PersonalDataExport\Jobs\CreatePersonalDataExportJob;

#[Layout('electrik::components.layouts.app')]
#[Title('Profile')]
class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $timezone = 'UTC';

    public string $locale = 'en';

    public $avatar;

    public string $delete_password = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->timezone = (string) ($user->timezone ?: config('app.timezone', 'UTC'));
        $this->locale = Locales::isSupported($user->locale ?? null)
            ? (string) $user->locale
            : (string) config('app.locale', 'en');
    }

    public function save(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->getAuthIdentifier()),
            ],
            'timezone' => ['required', 'string', 'timezone:all'],
            'locale' => ['required', 'string', Rule::in(Locales::codes())],
        ]);

        $emailChanged = strcasecmp((string) $user->email, $validated['email']) !== 0;

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'timezone' => $validated['timezone'],
            'locale' => $validated['locale'],
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        app()->setLocale($validated['locale']);

        if ($emailChanged && method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
        }

        session()->flash('status', __('Profile updated.'));

        // Full reload so layout + middleware pick up the new locale immediately.
        $this->redirect(route('settings.profile'), navigate: false);
    }

    public function updateAvatar(): void
    {
        $this->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();
        $path = $this->avatar->store('avatars/'.$user->getAuthIdentifier(), 'public');

        if ($user->avatar_path ?? null) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->forceFill(['avatar_path' => $path])->save();
        $user->refresh();
        $this->reset('avatar');

        session()->flash('status', __('Profile photo updated.'));
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar_path ?? null) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->forceFill(['avatar_path' => null])->save();
        }

        session()->flash('status', __('Profile photo removed.'));
    }

    public function requestDataExport(): void
    {
        abort_unless(
            class_exists(CreatePersonalDataExportJob::class),
            404
        );

        $user = Auth::user();

        abort_unless(
            $user instanceof \Spatie\PersonalDataExport\ExportsPersonalData,
            422,
            __('Your user model must implement ExportsPersonalData. Re-run php artisan electrik:install.')
        );

        // Queue the export (can grow large). Spatie emails a download link when the zip is ready.
        CreatePersonalDataExportJob::dispatch($user);

        session()->flash(
            'status',
            __('We are preparing your data export. You will receive an email with a download link when it is ready.')
        );
    }

    public function deleteAccount(DeleteTeam $deleteTeam): void
    {
        $this->validate([
            'delete_password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (! Hash::check($this->delete_password, $user->password)) {
            throw ValidationException::withMessages([
                'delete_password' => __('The password is incorrect.'),
            ]);
        }

        if (method_exists($user, 'ownedTeams') || method_exists($user, 'teams')) {
            $ownedTeams = method_exists($user, 'ownedTeams')
                ? $user->ownedTeams()->get()
                : $user->teams()->where('owner_id', $user->getAuthIdentifier())->get();

            foreach ($ownedTeams as $team) {
                $memberCount = method_exists($team, 'users')
                    ? $team->users()->count()
                    : 1;

                if ($memberCount > 1) {
                    throw ValidationException::withMessages([
                        'delete_password' => __('You own :team with other members. Transfer ownership or remove members before deleting your account.', [
                            'team' => $team->name,
                        ]),
                    ]);
                }
            }

            foreach ($ownedTeams as $team) {
                $deleteTeam->execute($team, $user);
            }

            if (method_exists($user, 'teams')) {
                foreach ($user->teams()->get() as $team) {
                    if (method_exists($user, 'detachTeam')) {
                        $user->detachTeam($team);
                    }
                }
            }
        }

        if ($user->avatar_path ?? null) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        Auth::logout();
        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('login'), navigate: true);
    }

    public function avatarUrl(): ?string
    {
        $user = Auth::user();

        if (! ($user->avatar_path ?? null)) {
            return null;
        }

        return Storage::disk('public')->url($user->avatar_path);
    }

    public function render()
    {
        return view('electrik::livewire.settings.profile', [
            'timezones' => Timezones::options(),
            'locales' => Locales::options(),
            'personalDataExportEnabled' => class_exists(CreatePersonalDataExportJob::class),
        ]);
    }
}
