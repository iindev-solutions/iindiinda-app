# Coolify Exact Setup Guide

This guide describes the exact first trial setup for the current `iindiinda-app` monorepo in Coolify.

## Goal

Run the current project in Coolify with:

- public `frontend` service
- same-origin `/api`
- internal Laravel Nginx + PHP-FPM split
- internal MySQL

## Recommended Rollout Strategy

Do **not** cut over production first.

Use this order:

1. keep current VPS deployment alive
2. deploy Coolify stack on a trial subdomain
3. verify browser + API + asset behavior
4. switch Telegram Mini App URL only after the trial is green
5. cut traffic over

Recommended trial domain example:

- `coolify.iindiinda.duckdns.org`
- or `beta.iindiinda.duckdns.org`

## 1. Prepare Inputs Before Coolify

You need these values ready:

| Variable | What to use |
|----------|-------------|
| `APP_URL` | trial domain first, later final production domain |
| `APP_KEY` | reuse current Laravel production key or generate a new one before first deploy |
| `DB_DATABASE` | new MySQL database name |
| `DB_USERNAME` | new MySQL username |
| `DB_PASSWORD` | strong password |
| `DB_ROOT_PASSWORD` | strong root password |
| `TELEGRAM_BOT_TOKEN` | current bot token |
| `NUXT_PUBLIC_TELEGRAM_BOT_ID` | numeric bot id |
| `RUN_MIGRATIONS` | `true` for single-instance deploy |

### Telegram Bot ID

For `NUXT_PUBLIC_TELEGRAM_BOT_ID`, use the numeric bot id.

Usually it is the numeric prefix before `:` in the bot token.

Example:

- token: `123456789:ABCDEF...`
- bot id: `123456789`

## 2. Create Resource In Coolify

In Coolify:

1. Create or open project
2. Create **New Resource**
3. Choose **Docker Compose**
4. Choose **Git-based deployment**
5. Connect repository `iindev-solutions/iindiinda-app`
6. Branch: `front/ayan`
7. Compose file path: `docker-compose.coolify.yml`
8. Base directory / build context: repository root

Do not point Coolify at `frontend/` or `backend/` separately for this setup.

This stack expects repository root.

## 3. Set Environment Variables In Coolify

Add these variables from `.env.coolify.example`.

Minimum set:

```env
APP_NAME=iind
APP_URL=https://coolify.iindiinda.duckdns.org
APP_KEY=base64:replace-me
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
LOG_CHANNEL=stack
LOG_LEVEL=info
DB_DATABASE=iind
DB_USERNAME=iind
DB_PASSWORD=replace-me
DB_ROOT_PASSWORD=replace-me
TELEGRAM_BOT_TOKEN=replace-me
NUXT_PUBLIC_TELEGRAM_BOT_ID=123456789
RUN_MIGRATIONS=true
```

### Important

- `APP_KEY` must be real before Laravel starts
- `APP_URL` must match the public frontend domain for this environment
- `NUXT_PUBLIC_TELEGRAM_BOT_ID` should not be empty if you want browser OAuth path available later
- frontend API base is already forced to `/api` by `docker-compose.coolify.yml` and `frontend/Dockerfile.coolify`

## 4. Public Domain Routing

Expose **only** `frontend` publicly.

### Public

- `frontend`

### Internal only

- `backend`
- `backend-web`
- `db`

Attach the trial domain to `frontend` only.

Do **not** expose `backend-web` directly.
Do **not** expose `backend` directly.
Do **not** expose MySQL publicly.

## 5. What Each Service Does

| Service | Purpose |
|---------|---------|
| `frontend` | serves Nuxt static files and proxies `/api/*` |
| `backend-web` | internal Nginx serving Laravel `public/` |
| `backend` | PHP-FPM Laravel runtime |
| `db` | MySQL 8.0 |

Routing path is:

```text
browser -> frontend -> /api/* -> backend-web -> backend -> mysql
```

## 6. First Deploy

Start first deploy in Coolify.

Expected behavior:

- `frontend` builds Nuxt static bundle with `NUXT_PUBLIC_API_BASE=/api`
- `backend` installs Laravel dependencies
- `backend` entrypoint runs:
  - `php artisan optimize:clear`
  - `php artisan storage:link`
  - `php artisan migrate --force` if `RUN_MIGRATIONS=true`
- `backend-web` serves Laravel public entry
- `db` initializes fresh MySQL data volume

## 7. First Verification Checklist

After deploy, verify these URLs on the Coolify domain:

### Core routes

- `/` -> `200`
- `/ayan` -> `200`
- `/agal` -> `200`
- `/legal` -> `200`
- `/api/health` -> `200`

### API behavior

- `/api/agal/routes` as guest -> expected auth failure (`401` JSON), not HTML redirect
- `/api/user` as guest -> `401`

### Asset behavior

Open page source or devtools and verify:

- built root HTML contains `apiBase:"/api"`
- real CSS/JS under `/assets/*` return `200`
- fake asset like `/assets/does-not-exist.js` returns `404`, not `index.html`

### Browser smoke

- home loads
- AYAN feed opens
- AGAL feed opens
- bottom nav works
- no MIME/type errors in browser console

## 8. Telegram Mini App Cutover

Do this **only after** trial deploy is green.

If the final Coolify domain differs from the current live domain:

1. update `APP_URL`
2. update Telegram bot Web App URL / menu button in BotFather
3. redeploy if env changed
4. retest login inside Telegram

If you reuse the current production domain, do the switch only when the Coolify stack is already verified.

## 9. Rollback Strategy

If Coolify deploy fails or Telegram login breaks:

- keep current VPS deployment serving production
- fix Coolify stack on trial domain
- do not switch bot Web App URL yet

This keeps live traffic safe.

## 10. Known Caveats For Current Starter

1. This stack is source-prepared but not yet runtime-verified in this repository
2. Local environment used for authoring did not have `docker`, so no local image build happened
3. `RUN_MIGRATIONS=true` is fine for one backend instance; if scaling later, move migrations to a safer release step
4. Browser OAuth path is not current production auth path; Telegram `initData` remains the main auth flow
5. Existing manual VPS deployment remains the current proven production path until this Coolify stack passes a real trial

## 11. Files Used By This Setup

- `docker-compose.coolify.yml`
- `frontend/Dockerfile.coolify`
- `backend/Dockerfile.coolify`
- `backend/docker/entrypoint.coolify.sh`
- `ops/coolify/frontend.nginx.conf`
- `ops/coolify/backend.nginx.conf`
- `.env.coolify.example`

## 12. Practical Next Step

Best next move:

1. create Coolify trial resource from `docker-compose.coolify.yml`
2. deploy on trial subdomain
3. send back first deploy logs/errors
4. patch only real failures
