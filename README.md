# Electrik 5.x

> Alpha. APIs will change. Built on [Electrik Slate](https://slate.electrik.dev) 3.x.

Laravel SaaS starter kit: authentication, teams, and Stripe billing — as a **Composer package** (code stays under `Electrik\`, not copied into `App\`).

**Coming from Slate?** Same UI kit — add the product shell: `composer require electrik/electrik:^5.0@alpha` → [install guide](https://electrik.dev/install) · [demo](https://demo.electrik.dev).

## License

**Business Source License 1.1** with a free Additional Use Grant for personal, educational, open-source, and pre-revenue indie use. Commercial use by companies and client work requires a commercial license. See [LICENSE](LICENSE).

## Requirements

- PHP 8.3+
- Laravel 12+
- Livewire 4
- Tailwind CSS v4
- `electrik/slate` `^3.0@alpha`
- `electrik/teamwork` `^11.0` (Electrik fork of Teamwork; Laravel 13 ready)
- `spatie/laravel-permission` `^6.0`
- `laravel/cashier` `^15.0`

## Install (alpha)

```bash
composer require electrik/electrik:^5.0@alpha
php artisan electrik:install
```

## Local sandbox

From the lab root (`electrik/` workspace folder):

```bash
./scripts/reset-electrik-sandbox.sh
cd electrik-sandbox
php artisan serve
```

That wipes `electrik-sandbox/`, creates a fresh Laravel app, path-links local `electrik` + `slate`, runs `electrik:install`, and builds assets.

Auth smoke pages after reset: `/login`, `/register`, `/forgot-password`.
Billing: `/billing` (set Stripe test keys in `.env`, then `php artisan electrik:stripe:sync`).

Local workspace (path-repo Slate inside the package):

```bash
# merge composer.local.json repositories into a consumer app, or use the sandbox script above
composer update electrik/slate
```

## Stack

- UI: anonymous `<x-slate::*>` components + Slate tokens
- Docs for UI: https://slate.electrik.dev
- Product site: https://electrik.dev

## Status

`5.0.0-alpha.7` ships auth, teams, billing, and account profile/password settings.
