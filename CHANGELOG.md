# Changelog

All notable changes to Electrik are documented in this file.

## [5.4.0] - 2026-08-29

### Added

- Sample **Studio** micro-app: Clients → Projects → Tasks (`/clients`, `/projects`, project show), gated by `ELECTRIK_SAMPLE_PROJECTS`.
- Richer Studio dashboard: client/project/task stats, my tasks, overdue, recent activity.
- `electrik:seed-demo` seeds a multi-client Studio dataset.
- Team brand color picker + live CSS var apply (`electrik:brand-updated`); contrast-aware primary foreground.
- Team archive/restore; optional login IP allowlist (`allowed_ips`) with `electrik.team-ip` middleware.
- Session device rename (`session_labels`); new-login email/database alerts (`ELECTRIK_LOGIN_ALERTS`).
- Apple / Microsoft Socialite providers (optional `socialiteproviders/*` packages).
- Stripe tax IDs on Billing Address; metered usage UI + `POST /api/electrik/usage`; plan add-ons (`is_addon` / `metered`).
- Outbound team webhooks (signed POST + deliveries); ops plan-features editor; ops email preview.
- Ops metrics: signups 7d/30d, active subscriptions, estimated MRR stub.
- API: `GET /api/electrik/team`, `GET /api/electrik/members` (token expiry / last_used on `/me`).

### Changed

- Package version `5.4.0`.
- Paddle remains unsupported; docs/roadmap treat Stripe as the only billing path.

### Fixed

- RTL: `text-left` → `text-start` on team switcher and invoices table.

## [5.3.0] - 2026-08-29

### Added

- Sanctum ability catalog on API token create UI; `tokens.manage` permission for team-scoped tokens; `electrik.ability` middleware.
- Authentication log filters and pagination on Sessions (`rappasoft/laravel-authentication-log`).
- Stripe Checkout promotion codes; past_due / unpaid dunning banner and Billing alert; `stripe_webhook_events` table + team webhook list.
- Social login (Google / GitHub via `laravel/socialite`) and magic-link email sign-in.
- Operator console at `/ops` (`ELECTRIK_OPERATOR_EMAILS`): users (suspend), teams, Stripe webhooks, failed jobs, announcements.
- Announcement banner + publish fan-out notifications; once-per-day past-due billing notifications.
- Per-team branding (`brand_logo_path`, `brand_primary`) on team settings and app chrome.
- Package API route `GET /api/electrik/me` (Sanctum + team token bind + throttle); `dedoc/scramble` suggested for OpenAPI.

### Changed

- Package version target `5.3.0`.
- `PlanFeatures` aligns seat-billed `max_seats` with member limits when features omit `max_members`.

## [5.2.0] - 2026-08-27

### Added

- GDPR personal data export and account deletion via `spatie/laravel-personal-data-export` (profile Privacy section; queued export emailed with download link).
- Passkeys via `laravel/passkeys` (Security settings management + login button; WebAuthn helper asset).
- Authentication log via `rappasoft/laravel-authentication-log` (login/logout/failed attempts on Sessions).
- MCP docs server ships in-repo under `mcp/` (`@electrik/electrik-mcp`).

### Changed

- Package version `5.2.0`.
- `electrik:install` wires personal data export + passkeys on the User model when packages are present.
- Billing remains Stripe (`laravel/cashier`) only; unfinished Paddle stub driver removed.

## [5.1.0] - 2026-08-27

### Added

- `electrik:make:livewire`, `electrik:make:model`, and `electrik:make:resource` generators with Slate-styled stubs.
- Multi-persona `electrik:seed-demo` (owner / admin / member + Demo Team and Acme Team + sample activity).
- White-label branding config: `ELECTRIK_LOGO_URL`, `ELECTRIK_LOGO_DARK_URL`, `ELECTRIK_BRAND_PRIMARY`, `ELECTRIK_SHOW_POWERED_BY`.
- Member impersonation via `lab404/laravel-impersonate` (`users.impersonate`) with stop banner and activity events.
- Team activity filters and pagination.

### Changed

- Package `VERSION` file aligned to stable `5.0.0` line (now shipping `5.1.0`).
- Team activity log now uses `spatie/laravel-activitylog` (`Electrik\Models\Activity` with `team_id`) instead of a custom `TeamActivityLog` table.
- Two-factor auth QR generation uses `pragmarx/google2fa-laravel` + `bacon/bacon-qr-code` (inline QR) instead of a third-party QR image API.

## [5.0.0] - 2026-08-26

### Added

- First stable `5.0.0` release of the Electrik Laravel SaaS starter kit.
- Depends on stable `electrik/slate` `^3.0` (no `@alpha` stability flag).

### Notes

- Install with `composer require electrik/electrik:^5.0`.
- Historical `5.0.0-alpha.*` tags remain available for pins.
- Package version is derived from `composer.json` at boot (`ElectrikServiceProvider::packageVersion()`).

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
