# Changelog

All notable changes to Electrik are documented in this file.

## [5.0.0-alpha.15] - 2026-08-24

### Added

- Carbon icons as first-class dependencies (`blade-ui-kit/blade-icons`, `codeat3/blade-carbon-icons`).
- Electrik welcome page stub published by `electrik:install` (replaces Laravel default when using `--force`).

### Fixed

- App shell and nav icons render via `@svg('carbon-…')` so consumer apps and the hosted demo match the sandbox icon set.

## [5.0.0-alpha.14] - 2026-08-22

### Added

- Profile photo upload on `/settings/profile`.
- Session management UI at `/settings/sessions` (requires `SESSION_DRIVER=database`).
- `electrik:seed-demo` for a ready-to-login demo workspace.
- Package-level PHPUnit suite and `scripts/ci-package.sh` (CI runs without monorepo sandbox).

### Changed

- `electrik:install` stubs `SESSION_DRIVER`, documents demo seed + storage link, runs permission sync with `--migrate`.
- CI workflow runs package tests on every push; sandbox integration tests when present.

## [5.0.0-alpha.13] - 2026-08-22

### Added

- Plan features and limits (`custom_roles`, `max_members`) with `EnsurePlanFeature` middleware.
- Seat billing sync via `SyncTeamSeats` when plans use per-seat pricing.
- Team activity log page and `ActivityLogger` for invites, membership, settings, and ownership changes.
- In-app notification bell (database notifications).
- Two-factor authentication (TOTP + recovery codes) on Security settings and login challenge.
- Sanctum API tokens UI at `/settings/api-tokens`; install wires `HasApiTokens`.
- Public pricing page at `/pricing`.
- Team avatar upload on team settings.
- Account menu on the primary rail and header dropdown.

### Changed

- Roles create route gated by `custom_roles` plan feature; invite flow respects member limits.
- `electrik:install` publishes Sanctum when available.

## [5.0.0-alpha.12] - 2026-08-22

### Added

- Onboarding wizard at `/onboarding` (team name → plan → invite).
- Subscription gate banner when `ELECTRIK_REQUIRE_SUBSCRIPTION=true`.
- Dashboard widgets: members, billing/trial status, pending invites.
- Billing setup health card (Stripe keys + webhook URL).
- Trial countdown on dashboard and billing overview.

### Changed

- New users redirect to onboarding after register/login/verify (skips for invite accept).
- `electrik:install` documents webhook URL and stubs onboarding/subscription env vars.

## [5.0.0-alpha.11] - 2026-08-22

### Added

- Transfer team ownership and delete team (settings, owner-only, with confirms).
- Branded 403 page for authorization failures.
- Sandbox feature tests (team auth, ownership, role scoping) and monorepo CI workflow.

### Changed

- Permission sync uses `Role::findOrCreateForTeam()`; optional `--cleanup-global-roles` removes null `team_id` roles.
- Invite pending role stored in session instead of a static property.
- Secondary sidebar links use route `{team}` when present; billing tabs removed from main content (live in sidebar).

## [5.0.0-alpha.10] - 2026-08-21

### Changed

- App shell uses a **primary** icon rail plus a **secondary** contextual sidebar (dashboard / teams / billing / account).

### Fixed

- Roles index filtered with `Role::forTeam()` so other teams’ roles no longer appear.
- Team pages authorize against the **route team** (bind Spatie team id + switch current team), closing cross-team `can()` privilege bugs.
- Locked Livewire `Team`/`Role` props; clear Spatie roles on leave/remove; invite assignable roles include custom team roles; re-apply invite role when already a member.
- App shell locks to the viewport so the sidebar stays full height while main content scrolls.

### Added

- Full invite journey: guest can open accept link, then sign in or register with locked invite email; after verify, returns to accept.
- Invite deny route + email decline link; invite `expires_at` (default 7 days); cancel/resend on members.
- `AcceptTeamInvite` action assigns Spatie role from the invite row (request context, not session).

### Changed

- Teamwork invite tokens use `Str::random(40)`.
- Invitees registering via an invite skip the default personal team.
- Richer invitation email (inviter, role, expiry, accept/decline).
- Require `dompdf/dompdf` so Cashier invoice PDF downloads work out of the box.

## [5.0.0-alpha.9] - 2026-08-21

### Changed

- Premium authenticated app shell: brand header, sidebar groups with active states, muted main canvas, max-width content.
- Shared `page-header` and `nav-link` components; polished list rows and cards across teams, billing, and settings.
- Billing sub-nav uses Slate tabs.
- Breadcrumbs via **[diglactic/laravel-breadcrumbs](https://github.com/diglactic/laravel-breadcrumbs)** + Slate view (`electrik::breadcrumbs`).
- Checkout success URL includes `session_id`; billing overview syncs the subscription if webhooks were missed. Added `php artisan electrik:stripe:sync-subscriptions`.
- Destructive confirms use Slate `alert-dialog` via `x-electrik::confirm` (no native browser `confirm()`).

## [5.0.0-alpha.8] - 2026-08-21

### Added

- Package Livewire dashboard at `/dashboard` (replaces sandbox `Route::view`).
- Leave team (self) on members page, with owner blocked until transfer/delete.
- Opt-in `EnsureSubscriptionActive` middleware (`ELECTRIK_REQUIRE_SUBSCRIPTION` / `billing.require_subscription`).
- Spatie permissions stack: catalog + `electrik:permissions:sync`, system roles, invite role, members role badges/change, Roles CRUD + Permissions index, `can()` gates on teams/billing.

## [5.0.0-alpha.7] - 2026-08-21

### Added

- Account settings: `/settings/profile` (name, email, timezone) and `/settings/security` (change password).
- Nullable `users.timezone` migration; sidebar Account links.

## [5.0.0-alpha.6] - 2026-08-21

### Added

- Team billing on Cashier: `/billing` overview, plans (Stripe Checkout subscribe/swap), subscription cancel/resume, payment methods + Customer Portal, billing address sync, invoices.
- Local Stripe catalog (`stripe_products` / `stripe_plans`) + `php artisan electrik:stripe:sync`.
- Cashier `subscriptions` / `subscription_items` migrations with `team_id`.
- Install stubs `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`, `CASHIER_CURRENCY`.

## [5.0.0-alpha.5] - 2026-08-21

### Changed

- Depend on **`electrik/teamwork`** (Laravel 10–13 fork of mpociot/teamwork) instead of upstream `mpociot/teamwork`.
- Sandbox reset path-links `../teamwork` and uses latest Laravel again.

## [5.0.0-alpha.4] - 2026-08-21

### Changed

- Teams now use **[mpociot/teamwork](https://github.com/mpociot/teamwork)** instead of a custom teams layer.
- Team switch is Livewire-only (no Electrik switch controller).
- `Electrik\Models\Team` extends Teamwork + Cashier `Billable`; Cashier columns added via migration on `teams`.

### Removed

- Custom `teams` / `team_user` / `team_invitations` migrations and `HasTeams` / team Actions.

## [5.0.0-alpha.3] - 2026-08-21

### Added

- Teams: create, list, settings, members, invite/accept, default team on register.
- Team switch via **POST** only (sidebar + index use forms).
- Spatie Permission with `teams => true` and `setPermissionsTeamId` on switch/middleware.
- Cashier `Billable` on **Team** with Stripe columns on `teams` (not users).
- Invite stores role as string (no premature FK to Spatie `roles`).

## [5.0.0-alpha.2] - 2026-08-21

### Changed

- Require **Livewire 4** (`livewire/livewire: ^4.0`).
- Auth full-page routes use `Route::livewire()` (Livewire 4 preferred routing).

## [5.0.0-alpha.1] - 2026-08-21

### Added

- Auth Livewire flows: login, register, forgot/reset password, email verification notice, logout.
- Remember-me via `Auth::attempt(..., $remember)` (fixes the 4.x cookie bug).
- Auth config keys: `electrik.auth.home`, `registration`, `email_verification`.
- `electrik:install` enables `MustVerifyEmail` on the app User model when verification is on.

## [5.0.0-alpha.0] - 2026-08-21

### Added

- Orphan `5.x` foundation: Composer package, service provider, config stub, guest/app layouts on Slate 3, and `electrik:install` stub.
