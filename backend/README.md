# iind Backend

Laravel API backend for `iindiinda-app`.

## Stack

- Laravel 11
- PHP 8.2+
- MySQL 8+
- Laravel Sanctum

## First Run

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Local API Checks

```bash
php artisan route:list
php artisan serve
```

Health endpoint:

```bash
curl http://127.0.0.1:8000/api/health
```

## Auth Notes

- `POST /api/auth/telegram` issues a real Sanctum token
- `GET /api/user` requires `Authorization: Bearer <token>`
- protected `api/*` routes return JSON `401`, not HTML redirect

## Deploy Update Flow

```bash
git pull --ff-only origin front/ayan
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```
