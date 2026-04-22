# AYAN Backend Bring-Up

> Part of: [[wiki/services/ayan/api-contract]]
> Goal: take current AYAN backend skeleton from static code to runnable Laravel API.

## Context

Current branch already contains:

- AYAN routes under `/api/ayan/trips`, `/api/ayan/requests`, `/api/ayan/responses`, `/api/ayan/my/*`
- Models: `User`, `Trip`, `AyanRequest`, `AyanResponse`
- Migrations: `users`, `trips`, `requests`, `responses`
- Controllers: `TripController`, `RequestController`, `ResponseController`, `MyController`

Current risk:

- this was prepared in an environment without `php`, `composer`, or `docker`
- code is contract-aligned, but not runtime-verified yet

## Success State

Bring-up is successful when all of this is true:

1. Laravel app boots without fatal errors
2. migrations run cleanly
3. `php artisan route:list` shows new AYAN routes, not old `/ayan/orders/*`
4. `/api/health` works
5. `/api/auth/telegram` returns usable auth token
6. protected `/api/ayan/*` routes accept token and return `{ success, data }`
7. frontend can switch from mock to real API without endpoint mismatch

## Preconditions

Need local machine or container with:

- PHP `8.2+`
- Composer `2+`
- MySQL `8+`
- working Laravel base in `backend/`

Required files to check first:

- `backend/composer.json`
- `backend/artisan`
- `backend/bootstrap/app.php`
- `backend/config/app.php`
- `backend/config/database.php`

If any file above is missing, stop. First task is restoring full Laravel app skeleton.

## Step 1 — Restore Laravel Runtime

In `backend/` run:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

If `artisan` or `composer.json` is missing:

1. restore backend from original Laravel scaffold, or
2. create new Laravel app in `backend/`, then re-apply AYAN files from current branch

## Step 2 — Configure Database

Set environment values:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iind
DB_USERNAME=iind
DB_PASSWORD=secret
```

Then run:

```bash
php artisan migrate:status
php artisan migrate
```

Expected tables:

- `users`
- `trips`
- `requests`
- `responses`

## Step 3 — Verify Route Registration

Run:

```bash
php artisan route:list
```

Must exist:

- `POST /api/auth/telegram`
- `GET /api/user`
- `POST /api/user/switch-role`
- `GET /api/ayan/trips`
- `POST /api/ayan/trips`
- `GET /api/ayan/trips/{id}`
- `PATCH /api/ayan/trips/{id}`
- `GET /api/ayan/requests`
- `POST /api/ayan/requests`
- `GET /api/ayan/requests/{id}`
- `PATCH /api/ayan/requests/{id}`
- `GET /api/ayan/trips/{id}/responses`
- `GET /api/ayan/requests/{id}/responses`
- `POST /api/ayan/trips/{id}/responses`
- `POST /api/ayan/requests/{id}/responses`
- `PATCH /api/ayan/responses/{id}`
- `DELETE /api/ayan/responses/{id}`
- `GET /api/ayan/my/trips`
- `GET /api/ayan/my/requests`
- `GET /api/ayan/my/responses`

Must not exist as active path:

- `/api/ayan/orders`
- `/api/ayan/orders/open`
- `/api/ayan/orders/my`

## Step 4 — Fix Auth Before API Testing

Current code still has mock auth behavior. Before real API verification:

1. install and configure Sanctum
2. make `User` actual auth model
3. change `/api/auth/telegram` from fake token response to real token issuance
4. map token back to user on protected routes

Checklist:

- `laravel/sanctum` installed
- Sanctum migrations run
- `HasApiTokens` enabled on `User`
- auth middleware works on `/api/user`
- `/api/auth/telegram` creates or finds user and returns valid token

## Step 5 — Smoke Test API

Start server:

```bash
php artisan serve
```

Health check:

```bash
curl http://localhost:8000/api/health
```

Login:

```bash
curl -X POST http://localhost:8000/api/auth/telegram \
  -H "Content-Type: application/json" \
  -d '{"init_data":"test"}'
```

Current user:

```bash
curl http://localhost:8000/api/user \
  -H "Authorization: Bearer TOKEN"
```

Create trip:

```bash
curl -X POST http://localhost:8000/api/ayan/trips \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"from_address":"Якутск","to_address":"Намцы","date":"2026-04-23","time":"09:00","seats":3,"price":500}'
```

List trips:

```bash
curl http://localhost:8000/api/ayan/trips \
  -H "Authorization: Bearer TOKEN"
```

Create request:

```bash
curl -X POST http://localhost:8000/api/ayan/requests \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"from_address":"Якутск","to_address":"Тулагино","date":"2026-04-23"}'
```

Create response:

```bash
curl -X POST http://localhost:8000/api/ayan/trips/1/responses \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"message":"Могу взять"}'
```

## Step 6 — Switch Frontend Off Mock

Only after backend smoke tests are green:

1. set `frontend/app/config/api.config.ts` -> `USE_MOCK_API = false`
2. run frontend
3. verify flows:
   - create trip
   - create request
   - list filters
   - trip detail
   - request detail
   - create response
   - accept / reject response
   - my trips / requests / responses

## Common Failure Modes

### `Class not found` / namespace errors

Cause: broken Laravel base, missing autoload refresh, wrong namespace.

Fix:

```bash
composer dump-autoload
```

Then re-run:

```bash
php artisan route:list
```

### `auth:sanctum` rejects token

Cause: fake token still returned from `/api/auth/telegram`.

Fix:

- replace fake `mock_token_*`
- issue real Sanctum token from authenticated user

### Migration fails on responses constraint

Cause: DB/version does not like `CHECK` constraint or table order.

Fix:

1. confirm MySQL version
2. if needed, keep nullable FKs and enforce xor rule in app validation until DB constraint is adjusted

### Frontend gets shape mismatch

Cause: backend payload differs from `frontend/services/ayan/app/types/ayan.ts`.

Fix:

- compare payloads field-by-field
- normalize through Laravel resources if needed

## Final Cleanup

After runtime is green:

1. delete legacy `backend/app/Http/Controllers/Ayan/OrderController.php`
2. remove old `/ayan/orders/*` mentions from docs
3. add real backend tests
4. update `vault/sprint.md`
5. update `vault/logs/changelog.md`
