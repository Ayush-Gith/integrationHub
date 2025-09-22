# Dev Release Notes

Latest changes (most recent first)

## 2025-09-21 — Chore: docs and error handling improvements
- Added comprehensive developer release notes and improved README.
- Strengthened error handling across controllers, jobs, and listeners; added logging and safer response messages.

## 2025-08-10 — Feature: Webhook ingest and processing
- Implemented webhook ingestion endpoint and storage (Webhook model). Webhooks are queued for background processing via events and listeners.

## 2025-07-15 — Feature: Integration management
- CRUD endpoints for user Integrations with secure ownership checks and background product sync job dispatching.

## 2025-06-02 — Feature: Product model & sync
- Product model with association to Integrations. Products can be queued for sync; `SyncProductJob` handles updateOrCreate logic.

## 2025-05-20 — Feature: JWT auth
- User registration and login using JWT tokens (custom claims included). Provides auth endpoints and `me` endpoint.

Core modules and features

- Auth (app/Http/Controllers/AuthController.php)
  - JWT-based authentication with registration, login, and profile endpoints.
  - Adds custom claims (name, email, role, is_admin).

- Integrations (app/Http/Controllers/IntegrationController.php, app/Models/Integration.php)
  - Create, list, update, show, and delete integrations tied to users.
  - Dispatches product sync jobs when integrations are created/updated.

- Products (app/Http/Controllers/ProductController.php, app/Models/Product.php)
  - CRUD operations for products owned by a user's integrations.
  - Queues product sync work to `products` queue.

- Webhooks (app/Http/Controllers/WebhookController.php, app/Models/Webhook.php)
  - Receives, logs, and stores incoming webhook payloads. Emits `WebhookReceived` event.

- Jobs (app/Jobs/SyncProductJob.php, app/Jobs/ProcessWebhookJob.php)
  - `SyncProductJob` validates ownership and upserts products in background.
  - `ProcessWebhookJob` marks webhook events as processed (placeholder business logic).

Notes for developers

- Error handling: recent changes added try/catch around risky operations and log traces for debugging.
- Queues: uses Redis queues; check `queue.php` in `config` and ensure Redis is running locally or in CI.
- DB: Laravel migrations and seeders available under `database/migrations` and `database/seeders`.

Contact

For questions about architecture or contributing, open an issue or reach out to the repository maintainers.
