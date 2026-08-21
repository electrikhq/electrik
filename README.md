# Electrik 5.x

> Alpha. APIs will change. Built on [Electrik Slate](https://slate.electrik.dev) 3.x.

Laravel SaaS starter kit: authentication, teams, and Stripe billing — as a **Composer package** (code stays under `Electrik\`, not copied into `App\`).

## License

**Business Source License 1.1** with a free Additional Use Grant for personal, educational, open-source, and pre-revenue indie use. Commercial use by companies and client work requires a commercial license. See [LICENSE](LICENSE).

## Requirements

- PHP 8.3+
- Laravel 12+
- Livewire 3
- Tailwind CSS v4
- `electrik/slate` `^3.0@alpha`

## Install (alpha)

```bash
composer require electrik/electrik:^5.0@alpha
php artisan electrik:install
```

Local workspace (path-repo Slate):

```bash
# from this repo
cp composer.local.json composer.json.merge   # or merge repositories manually
composer update electrik/slate
```

## Stack

- UI: anonymous `<x-slate::*>` components + Slate tokens
- Docs for UI: https://slate.electrik.dev
- Product site: https://electrik.dev

## Status

`5.0.0-alpha.0` is a blank-line bootstrap. Auth, teams, and billing land in later alphas.
