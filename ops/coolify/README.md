# Coolify Starter Layout

This repository now includes a first-pass Coolify deployment layout for the current monorepo.

## Files

- `docker-compose.coolify.yml` — multi-service stack for Coolify
- `frontend/Dockerfile.coolify` — builds the Nuxt static bundle and serves it with Nginx
- `backend/Dockerfile.coolify` — Laravel PHP-FPM target plus internal Nginx target
- `backend/docker/entrypoint.coolify.sh` — Laravel startup hook for storage link + migrations
- `ops/coolify/frontend.nginx.conf` — SPA static serving + `/api` reverse proxy
- `ops/coolify/backend.nginx.conf` — internal Laravel Nginx config
- `.env.coolify.example` — minimum environment variables to configure in Coolify

## Service Shape

- `frontend` — public service, serves SPA and proxies `/api/*` to `backend-web`
- `backend` — internal PHP-FPM Laravel runtime
- `backend-web` — internal Nginx for Laravel public entry
- `db` — MySQL 8.0

## Important Notes

1. Keep frontend API base on `/api`
2. Expose only `frontend` publicly in Coolify
3. Configure persistent volumes for:
   - `mysql_data`
   - `backend_storage`
   - `backend_cache`
4. `RUN_MIGRATIONS=true` runs `php artisan migrate --force` on backend start
5. This is a starting point for Coolify migration, not yet live-verified in this repository

## Suggested Coolify Setup

See `ops/coolify/SETUP.md` for the exact step-by-step trial deployment flow.

Short version:

1. Create a new Docker Compose resource
2. Point it at `docker-compose.coolify.yml`
3. Set the domain on `frontend`
4. Add variables from `.env.coolify.example`
5. Deploy and verify:
   - `/`
   - `/ayan`
   - `/agal`
   - `/api/health`
   - current `/assets/*` files return `200`
