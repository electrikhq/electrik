<?php

namespace Electrik\Http\Controllers\Auth;

use Electrik\Support\Auth as ElectrikAuth;
use Electrik\Support\Onboarding;
use Electrik\Support\UserModel;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController
{
    /**
     * Built-in Socialite drivers plus optional SocialiteProviders packages.
     *
     * @var list<string>
     */
    protected const SUPPORTED_PROVIDERS = ['google', 'github', 'apple', 'microsoft'];

    public function redirect(string $provider): RedirectResponse
    {
        $this->ensureProviderEnabled($provider);

        $driver = Socialite::driver($provider);

        if ($provider === 'apple' && method_exists($driver, 'scopes')) {
            $driver->scopes(['name', 'email']);
        }

        return $driver->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $this->ensureProviderEnabled($provider);

        try {
            $socialiteUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('login')->with('error', __('Unable to sign in with :provider.', [
                'provider' => ucfirst($provider),
            ]));
        }

        $email = $socialiteUser->getEmail();

        if (blank($email)) {
            return redirect()->route('login')->with('error', __('Your :provider account has no email address we can use.', [
                'provider' => ucfirst($provider),
            ]));
        }

        $user = $this->findOrCreateUser($provider, $socialiteUser->getId(), $email, $socialiteUser->getName());

        if (! $user) {
            return redirect()->route('login')->with('error', __('No account found for that email.'));
        }

        if (ElectrikAuth::isSuspended($user)) {
            return redirect()->route('login')->with('error', __('This account has been suspended.'));
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->to(Onboarding::homePath());
    }

    protected function findOrCreateUser(string $provider, string $providerId, string $email, ?string $name): ?object
    {
        $table = UserModel::query()->getModel()->getTable();
        $hasProviderColumns = Schema::hasColumn($table, 'provider') && Schema::hasColumn($table, 'provider_id');

        $user = $hasProviderColumns
            ? UserModel::query()->where('provider', $provider)->where('provider_id', $providerId)->first()
            : null;

        $user ??= UserModel::query()->where('email', $email)->first();

        if ($user) {
            if ($hasProviderColumns && (blank($user->provider) || blank($user->provider_id))) {
                $user->forceFill([
                    'provider' => $provider,
                    'provider_id' => $providerId,
                ])->save();
            }

            return $user;
        }

        if (! config('electrik.auth.registration', true)) {
            return null;
        }

        $attributes = [
            'name' => filled($name) ? $name : (string) str($email)->before('@'),
            'email' => $email,
            'password' => Hash::make(Str::random(40)),
            'email_verified_at' => now(),
        ];

        if ($hasProviderColumns) {
            $attributes['provider'] = $provider;
            $attributes['provider_id'] = $providerId;
        }

        $user = UserModel::query()->create($attributes);

        event(new Registered($user));

        return $user;
    }

    protected function ensureProviderEnabled(string $provider): void
    {
        $configured = (array) config('electrik.auth.socialite.providers', []);

        abort_unless(
            in_array($provider, static::SUPPORTED_PROVIDERS, true) && in_array($provider, $configured, true),
            404
        );

        if (in_array($provider, ['apple', 'microsoft'], true)) {
            $this->ensureSocialiteProviderPackage($provider);
        }
    }

    protected function ensureSocialiteProviderPackage(string $provider): void
    {
        $map = [
            'apple' => \SocialiteProviders\Apple\Provider::class,
            'microsoft' => \SocialiteProviders\Microsoft\Provider::class,
        ];

        abort_unless(
            isset($map[$provider]) && class_exists($map[$provider]),
            503,
            __('The :provider login package is not installed. Require socialiteproviders/:provider.', [
                'provider' => $provider,
            ])
        );
    }
}
