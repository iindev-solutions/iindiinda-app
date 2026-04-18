# Laravel API Overview

## Backend Stack
- PHP 8.x / Laravel
- MySQL база данных
- RESTful API
- Laravel Sanctum (token auth)

## Current State
**Scaffold-only**: роуты определены в api.php, но 0 контроллеров, 0 моделей, 0 миграций.

## Key Files
- `backend/routes/api.php` — API маршруты (single source of truth)
- `backend/app/Http/Controllers/` — контроллеры (empty, .gitkeep)
- `backend/app/Models/` — модели Eloquent (empty, .gitkeep)
- `backend/database/migrations/` — миграции БД (empty, .gitkeep)

## Auth Pattern
- Telegram WebApp `init_data` → `POST /auth/telegram` → JWT token
- All ayan routes: `auth:sanctum` middleware
- Headers: `Authorization: Bearer {token}`

## Ayan Endpoints
See: [[wiki/services/laravel-api/endpoints]]

## API Contract (full)
See: [[wiki/architecture/api-contract]]

## Required Backend Changes (for 6-status flow)
1. Add `POST /ayan/orders/{id}/arrive`
2. Add `POST /ayan/orders/{id}/start`
3. Add `GET/POST /user/availability`
4. Fix: `GET /user` (not `/user/me`)
5. Add timestamps: accepted_at, arrived_at, started_at, completed_at, cancelled_at
6. Add fields: rating, cancel_reason
7. Implement all controllers + migrations
