# Integrations Hub — README

This repository contains a **Laravel 8 project** that demonstrates a production-grade backend with:

* JWT Authentication (Login/Register)
* Products / Integrations / Webhooks CRUD
* Queue-based background job processing
* Events & Listeners for async handling
* PHPUnit feature tests

Perfect for **interview demos** where you want to show full-stack backend skills.

---

## 🛠 Setup Instructions

```bash
## Integrations Hub

Integrations Hub is a small Laravel backend for ingesting webhooks, managing third-party integrations, and syncing product data via background jobs. It's built to be easy to run locally and extend.

Key features
- JWT-based authentication (register, login, profile)
- Manage user Integrations with secure ownership checks
- CRUD operations for Products tied to Integrations
- Webhook ingestion endpoint (stores payloads and fires events)
- Queue-based background processing (Redis queues)

Tech stack
- PHP 7.4+ / Laravel 8
- Redis for queueing
- MySQL / MariaDB (or other supported DB)
- JWT Auth (tymon/jwt-auth)

Prerequisites
- PHP 7.4 or newer
- Composer
- Redis (for queues)
- A database (MySQL/Postgres)

Installation & setup
1. Clone the repository and install dependencies:

	cd integrations-hub
	composer install

2. Copy environment and configure DB/Redis credentials:

	cp .env.example .env
	# Edit .env to set DB_*, REDIS_*, and APP_URL

3. Generate app key and JWT secret:

	php artisan key:generate
	php artisan jwt:secret

4. Run migrations and seeders:

	php artisan migrate --seed

5. Start the app and a queue worker in separate terminals:

	php artisan serve
	php artisan queue:work --queue=products,default

API Overview
- Authentication
  - POST /api/register — register a new user (name, email, password)
  - POST /api/login — obtain a JWT token (email, password)
  - GET /api/me — current user (requires Bearer token)

- Integrations (require auth)
  - GET /api/integrations — list current user's integrations
  - POST /api/integrations — create an integration (platform, api_key)
  - GET /api/integrations/{id} — show integration
  - PUT/PATCH /api/integrations/{id} — update
  - DELETE /api/integrations/{id} — delete

- Products (require auth)
  - GET /api/products — list products for user's integrations
  - POST /api/products — create/sync a product (queues SyncProductJob)
  - GET/PUT/DELETE /api/products/{id} — show/update/delete product

- Webhooks (public)
  - POST /api/webhooks/{platform} — receive webhook payloads

Running tests
 - php artisan test

Contribution
- Fork the repo, create a feature branch, and open a pull request with focused changes. Keep API compatibility and add tests for new behavior.

Support
- Open an issue or contact maintainers for architecture questions.
