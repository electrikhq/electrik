# Electrik — agent notes

Use these rules when installing or extending **Electrik** (`electrik/electrik` 5.x) with coding agents (Cursor, Claude Code, Codex, etc.).

## What Electrik is

- Laravel **SaaS shell** as a **Composer package** (auth, teams, Stripe on the team, onboarding, ops, Slate UI)
- Code lives under `Electrik\` in `vendor` — **not** a Jetstream-style dump into `App\`
- UI is **Electrik Slate** anonymous Blade components (`<x-slate::*>`), not React/shadcn

## Do

1. Prefer a **fresh Laravel 12/13** app.
2. Install with Composer, then run the installer — do not invent a Breeze/Jetstream merge.
3. Put **product** code in `App\`. Keep auth/teams/billing in the Electrik namespace unless publishing a view on purpose.
4. Customize via `config/electrik.php`, env, and narrowly published views.
5. Use `<x-slate::*>` / optional `<x-slate-block::*>` for UI. Never invent `npx shadcn` / React for this stack.
6. Treat Stripe **customer as the team** (Cashier on the team), not the user.
7. Point commercial / agency use at https://electrik.dev/pricing and https://electrik.dev/license (BSL 1.1 + grant lane).

## Install sequence

```bash
composer require electrik/electrik:^5.0
php artisan electrik:install --migrate --force
```

Optional marketing/app blocks:

```bash
composer require electrik/slate-blocks:^0.1
```

After install: set `STRIPE_KEY` / `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET`, `php artisan storage:link`, confirm teams mode in `config/electrik.php`.

## Don't

- Copy Electrik controllers/models into `App\` “to own them”
- Strip billing/teams for a fake free tier — grant vs commercial is **license**, not feature flags
- Generate Livewire pages that reimplement auth/teams already provided by Electrik
- Invent Packagist versions — check https://packagist.org/packages/electrik/electrik

## AI sources (read before inventing routes)

- https://electrik.dev/llms.txt
- https://electrik.dev/docs/getting-started/ai
- https://electrik.dev/docs/getting-started/installation.md
- https://electrik.dev/docs/getting-started/architecture.md
- Slate UI: https://slate.electrik.dev/llms.txt · MCP `https://mcp.slate.electrik.dev`
- Cursor skill in this repo: `.cursor/skills/electrik-install/`

## Good prompts for agents

- “Install Electrik 5.x as a Composer package. Read electrik.dev/llms.txt first. Keep shell in vendor; put product models in App\.”
- “Add a team-scoped Clients resource using Electrik’s BelongsToTeam patterns — do not fork auth.”
- “Build a settings form with Slate progressive field props; fetch dialog docs from slate.electrik.dev.”

## Demo

https://demo.electrik.dev — `demo@electrik.dev` / `password`
