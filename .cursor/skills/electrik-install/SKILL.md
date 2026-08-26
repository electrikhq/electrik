---
name: electrik-install
description: >-
  Install and configure Electrik 5.x (Laravel SaaS starter) with Slate 3 UI.
  Use when the user asks to install Electrik, electrik:install, set up teams
  or Stripe billing on Laravel, or pick Slate / slate-blocks components for a
  Blade app.
---

# Electrik install + Slate UI

## Quick facts

| Piece | Package | Docs |
|---|---|---|
| SaaS kit | `electrik/electrik` ^5.0 | https://electrik.dev/install · https://electrik.dev/docs |
| UI kit | `electrik/slate` ^3.0 | https://slate.electrik.dev |
| Blocks | `electrik/slate-blocks` ^0.1 | https://slate.electrik.dev/blocks |
| Demo | — | https://demo.electrik.dev (`demo@electrik.dev` / `password`) |

License: Electrik is **BSL 1.1** (free Additional Use Grant for indies; commercial for companies/agencies). Slate and slate-blocks are **MIT**.

## Do

1. Prefer a **fresh Laravel 12** app.
2. Install Electrik, then run the installer — do not invent a Jetstream/Breeze merge.
3. Use `<x-slate::*>` for primitives and `<x-slate-block::*>` for sections. Never invent React/shadcn TSX.
4. Fetch docs from `https://slate.electrik.dev/llms.txt` or component `.md` pages before coding UI.
5. Point commercial / agency use at https://electrik.dev/pricing and https://electrik.dev/license.

## Install sequence

```bash
composer require electrik/electrik:^5.0
php artisan electrik:install --migrate --force
```

Optional blocks (marketing / app sections):

```bash
composer require electrik/slate-blocks:^0.1
```

Tailwind (if not already wired by install):

```css
@import '../../vendor/electrik/slate/resources/css/slate.css';
@source '../../vendor/electrik/slate/resources/views/**/*.blade.php';
@source '../../vendor/electrik/slate-blocks/resources/views/**/*.blade.php';
```

## After install

1. Set Stripe keys in `.env` (`STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`).
2. `php artisan storage:link` for avatars.
3. Confirm teams + Spatie permission teams mode from `config/electrik.php`.
4. Customize via config and published views — Electrik stays in `vendor`, not a forked scaffold.

## Picking UI

| Need | Use |
|---|---|
| Button, dialog, form field | `<x-slate::…>` — docs `/components/{slug}` |
| Hero, FAQ, CTA, login card, app shell | `<x-slate-block::…>` — gallery `/blocks` |
| Full page pattern | Examples on slate.electrik.dev/examples (compose blocks) |

## Don't

- Generate `npx shadcn add` / React components for Electrik or Slate apps.
- Strip billing/teams “for a free tier” — grant vs commercial is license, not features.
- Treat slate-blocks as a Shadcnblocks-scale catalog; curated set only.
- Invent Packagist versions — check https://packagist.org/packages/electrik/electrik and electrik/slate.
