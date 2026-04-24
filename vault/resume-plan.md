# Resume Plan — 2026-04-23

> Goal: restore the exact current state quickly and continue work without depending on chat history.

## Stop Point

- Current branch: `front/ayan`
- Local `front/ayan` and `origin/front/ayan` are aligned at `87a4815` `feat(ayan): polish entry form and detail nav`
- Remote branch already contains the AYAN hardening commit: `755f7c6` `fix(ayan): enforce auth and response rules`
- Latest shipped UX slice now also includes:
  - visible detail-page back button in both browser UI and Telegram context
  - normal text price field with trailing `₽`
  - Nuxt UI calendar date picker with past dates disabled
- Local frontend dev flow against VPS uses `frontend/.env` with:
  - `NUXT_PUBLIC_API_BASE=http://89.22.226.34/api`
  - optional `NUXT_PUBLIC_DEV_INIT_DATA=test`
- VPS alias is defined locally as `iind-vps -> root@89.22.226.34`
- Local workspace is currently dirty only in documentation/vault files; no new frontend/backend code drift was found in this audit
- GitHub Pages is live:
  - `/` responds `200`
  - `/ayan` responds `200`
  - rebased assets under `/iindiinda-app/assets/*` respond correctly
- VPS frontend HTTP deploy now also works:
  - `http://89.22.226.34/` serves the frontend SPA
  - `http://89.22.226.34/ayan` falls back to SPA entry correctly
  - `http://89.22.226.34/api/health` still returns `200`
- VPS HTTPS deploy is now live:
  - `https://iindiinda.duckdns.org/` serves the frontend SPA
  - `https://iindiinda.duckdns.org/api/health` returns `200`
  - `http://iindiinda.duckdns.org/` redirects to HTTPS
- Auth-gate slice is committed and deployed:
  - AYAN shows Telegram-only access state for unauthenticated browser users
  - AYAN shows retryable auth-failed state for failed Telegram auth
  - production SPA no longer ships `devInitData:test`
- Previous direct AYAN VPS smoke was green for:
  - `POST /auth/telegram`
  - create trip/request
  - respond
  - accept
  - `my/*`
- The VPS runtime is reachable again from this machine:
  - `ssh iind-vps` succeeds
  - `curl -I http://89.22.226.34/api/health` returns `200`
  - focused backend feature tests pass on the clean synchronized checkout (`13 tests, 94 assertions`)
- VPS checkout state during this audit:
  - current HEAD includes the AYAN role-switch/past-item/VPS-SPA slice after pull from `origin/front/ayan`
  - tracking `origin/front/ayan`
  - worktree is clean after sync
- Previous dirty VPS state was preserved before sync:
  - backup patch: `/root/iind-backups/backend-dirty-20260423.patch`
  - backup status: `/root/iind-backups/backend-dirty-20260423.status`
  - backup untracked list: `/root/iind-backups/backend-untracked-20260423.list`
  - backup untracked archive: `/root/iind-backups/backend-untracked-20260423.tgz`
  - git stash: `stash@{0}` `pre-sync-20260423`
- Latest local hardening slice already pushed:
  1. `AuthController` no longer accepts forged Telegram payloads the old way
  2. `init_data=test` is limited to `local/testing`
  3. AYAN role and owner rules are enforced in backend code
  4. frontend detail pages no longer fetch owner-only response lists for non-owners
- Current blocker:
  - clean deployment verification is no longer blocked and has already passed
  - role switching UX is implemented and deployed, but still needs manual live verification against the real backend
  - browser auth also remains intentionally limited outside final Telegram flow
  - the new past-item/backend-expiry slice is now deployed and focused backend tests are green on the real VPS checkout
  - HTTPS is now available through DuckDNS, but live browser/TMA verification is still pending
  - the previous live TMA blocker (missing `TELEGRAM_BOT_TOKEN`) has been fixed on VPS and now needs manual retest from the real bot

## Session Restart Prompt

Use this prompt at the start of the next session:

```text
Read vault/master_index.md, vault/WORKFLOW.md, vault/sprint.md, and vault/resume-plan.md.
Current task: verify the AYAN role switcher and past-item UX against https://iindiinda.duckdns.org and confirm Telegram/TMA auth behavior on the HTTPS deployment, then decide whether to commit the new frontend auth-gate slice.

Current task: verify the full AYAN flow on https://iindiinda.duckdns.org in Telegram Mini App, including role switching, detail back navigation, price input, and calendar date restrictions.
```

## Safe Next-Step Plan

1. Manually test role-aware flows against live backend/front on VPS:
   - default login lands as `passenger`
   - passenger can create request and respond to trip
   - driver can create trip and respond to request
   - owners can see/accept/reject responses
2. Check the new UX details on the live deployment:
   - detail-page back button is visible and works
   - trip price field no longer shows stepper controls
   - calendar blocks past dates
3. Re-test Telegram/TMA integration on `https://iindiinda.duckdns.org`
4. Confirm guest browser users now see Telegram-only access state instead of broken AYAN actions
5. If needed later, replace DuckDNS with the final domain and re-issue TLS
6. Record the result in:
   - `vault/logs/changelog.md`
   - `vault/sprint.md`
   - `vault/resume-plan.md`

## Frontend State

- AYAN frontend is already on the real API (`USE_MOCK_API = false`)
- Implemented features:
  - trips/requests feed
  - filters
  - unified create slideover
  - AYAN role switcher in page header
  - trip/request detail pages
  - responses
  - accept/reject flow
  - Telegram contact link
- Local frontend verification already passed:
  - `npm run typecheck`
  - `npm run lint`
  - `npm run test`
- Browser auth remains intentionally limited until the Telegram flow is fully finalized end-to-end
- VPS frontend static deploy status:
  - build uploaded to `/var/www/iind-app/frontend/public`
  - Nginx default site now serves SPA at `/`
  - `/api/*` remains backed by Laravel
  - HTTPS hostname now live at `iindiinda.duckdns.org`

## Backend State

- VPS backend runtime already exists and serves AYAN endpoints
- Database migrations for `users`, `trips`, `requests`, `responses`, and `personal_access_tokens` were previously run on MySQL
- `AuthController` issues Sanctum tokens
- `TripController`, `RequestController`, `ResponseController`, and `MyController` use MySQL persistence
- Guest API transport already returns JSON `401`
- Focused VPS backend feature tests are currently green on clean deployed state (`13 tests, 94 assertions`)
- Focused VPS backend feature tests are currently green on the latest deployed checkout (`15 tests, 112 assertions`)
- AYAN runtime routes are registered on VPS via `php artisan route:list --path=api/ayan`
- VPS checkout is now clean and aligned to `1fd837f`
- Latest local backend hardening adds:
  - signed Telegram `initData` parsing
  - scoped local/testing fallback
  - role restrictions for create/respond actions
  - owner-only response list access
  - duplicate/closed-response guards
  - single accepted response guard
- Runtime verification of the committed hardening is complete on the clean synchronized VPS checkout
- Backend role logic summary:
  - new user login defaults to `passenger`
  - `POST /api/user/switch-role` updates role
  - only `driver` can create trips and respond to requests
  - only `passenger` can create requests and respond to trips
  - only order owner can list responses and accept/reject them
- New local backend slice adds:
- New deployed backend slice adds:
  - public feed filtering for expired open trips/requests
  - expired target guards for response creation/update
- Verification status for that deployed backend slice:
  - real VPS checkout feature tests: green (`15 tests, 112 assertions`)
  - API health route: green

## Main Findings

### What Is Already Solid

1. AYAN frontend is already switched to the real API
2. GitHub Pages deployment is live
3. AYAN backend runtime already exists on VPS
4. VPS now also serves the frontend SPA over HTTP from the same machine
5. The branch already contains the latest auth/authorization hardening
6. The clean deployed VPS checkout now verifies that hardening successfully

### What Blocks The Next Stage

1. Browser auth remains intentionally constrained
2. Local backend execution is impossible in this environment because `php`, `composer`, and `docker` are unavailable
3. Live browser/TMA verification for the new role-switch/past-item/detail-nav flow is still pending

## Priority Order

### P0 — Restore Operational Access

- manually verify role-aware live flows from frontend/TMA
- confirm the new HTTPS DuckDNS deployment works correctly in TMA/browser

### P1 — Verify AYAN Hardening On VPS

- backend verification already complete
- record manual frontend role-flow verification in `vault/`

### P2 — Continue Integration Work

- continue frontend/browser verification only after VPS verification is complete
- then continue with the next AYAN slice

## One-Line Summary

The backend and frontend slices are committed, pushed, and deployed on secure DuckDNS HTTPS, and the next real step is full manual Telegram/browser verification of the polished AYAN flow.
