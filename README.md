# Electrik 4.x

> Electrik 4.x is under active development. APIs may evolve, but the project is alive and progressing.

Electrik is a **source-available SaaS starter kit** designed to accelerate building modern SaaS applications with Laravel.

It provides a solid, opinionated foundation for authentication, teams, billing, and dashboards — so you can focus on your product instead of rebuilding the basics.

Built with **Laravel 12**, **Livewire 3**, and **Tailwind CSS 4**.

---

## Features

- **Team Management**  
  Multi-user and multi-team support out of the box

- **Subscription Billing**  
  Stripe-based recurring billing and plan management

- **User & Access Management**  
  Roles, permissions, and profile management

- **Scalable Dashboard**  
  Clean, minimal dashboard that grows with your product

- **Modern Architecture**  
  Event-driven, maintainable, and easy to extend

---

## Requirements

- PHP 8.3+
- Laravel 12.x
- Composer
- Node.js & NPM

---

## Quick Start

Electrik is intended to be installed on a fresh Laravel application.

1. Create a new Laravel project:
```bash
composer create-project laravel/laravel your-saas-app
````

2. Install Electrik:

```bash
composer require electrik/electrik
```

3. Run the installer:

```bash
php artisan electrik:install
```

4. Configure Stripe in `.env`:

```env
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

5. Sync plans from Stripe:

```bash
php artisan electrik:stripe:sync
```

6. Start the development server:

```bash
php artisan serve
```

Visit: [http://localhost:8000/dashboard](http://localhost:8000/dashboard)

> **Note**
> Subscription plans are synced from Stripe and stored locally.
> Re-run `php artisan electrik:stripe:sync` whenever plans change.

---

## Architecture

Electrik follows modern Laravel best practices:

* **Event-driven** — side effects handled via listeners
* **12-factor principles** — environment-based configuration
* **KISS** — simple, readable, intentional code
* **DRY** — reusable components, minimal duplication

---

## Package Structure

```
electrik/
├── src/
│   ├── Actions/          # Single-purpose actions
│   ├── Events/           # Domain events
│   ├── Listeners/        # Event listeners
│   ├── Services/         # Business logic
│   ├── Repositories/     # Data access
│   ├── Models/           # Eloquent models
│   ├── Livewire/         # UI components
│   ├── Requests/         # Validation
│   ├── Notifications/   # Notifications
│   └── Console/          # Artisan commands
├── config/
├── database/
├── resources/
└── routes/
```

---

## Contributing

Contributions are welcome — especially bug fixes, documentation improvements, and discussions.

If you're using Electrik for learning, experimentation, or open-source work, feel free to fork and explore.

---

## License

Electrik is licensed under the **Business Source License (BSL)**.

**In short:**

* Free for:

  * Personal projects
  * Indie hackers and solo developers
  * Open-source projects
  * Educational use
  * Pre-revenue experimentation

* A commercial license is required for:

  * Companies or organizations
  * Client, freelance, or agency work
  * Internal business tools
  * Commercial products or services
  * Any use as part of paid employment

Each released version of Electrik automatically becomes **fully open source (Apache 2.0)** four years after its release.

See the [`LICENSE`](./LICENSE) file for full terms.

---

## Sponsors

If Electrik helps your business, consider supporting its continued development by purchasing a commercial license.

