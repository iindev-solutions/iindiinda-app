# Resume Plan - 2026-04-24 20:15

> Goal: restart fast with exact stop point and no hidden chat memory.

## Legal Update - 2026-04-25 14:30

- Audited all four service visions and current frontend copy against the intended RF legal posture
- Expanded source-only legal center beyond AYAN with platform docs and service rules routes for AYAN, UUS, TAL, and AGAL
- Added common legal access in source via home legal card + shared footer legal bar in default layout
- Tightened risky placeholder copy in source:
  1. TAL no longer positions itself around clinics/medical scenarios
  2. AGAL no longer promises worldwide/courier-style delivery
  3. UUS no longer implies ratings/verification that do not exist on MVP
- Biggest unresolved compliance blocker remains RF personal-data localization: current runtime IP `89.22.226.34` geolocates to Sweden
- Final missing production legal inputs: real operator/controller details, formal requisites, and licensed RF legal review before deploy
- Latest owner clarification for future sessions:
  1. operator is planned as a natural person
  2. server/personal-data hosting is planned to move to RF later
  3. final public contact details are still pending

## Hotfix Update - 2026-04-25 09:25

- Investigated live Telegram/browser errors: blocked JS/CSS with MIME `text/html` and dynamic import failures
- Root cause confirmed: deploy asset drift on VPS (`/var/www/iind-app/frontend/public/assets`) where hashed files referenced by `e790UDQL.js` were missing
- Uploaded missing asset files from local `frontend/.output/public/assets` to VPS; affected URLs now return correct MIME (`application/javascript` / `text/css`)
- Remaining hardening task: adjust Nginx so unknown `/assets/*` returns `404` (not SPA HTML fallback) and split cache policy between `index.html` and hashed assets

## Hotfix Update - 2026-04-25 09:42

- Replaced partial hotfix with full deployment flow: rebuilt SPA locally and redeployed complete `frontend/.output/public` bundle to VPS
- Deployment now uses full directory swap (`public_new` -> `public`) and keeps `public_prev` as rollback/cache-compat safety
- Added old hashed assets back into current `public/assets` without overwriting fresh files to reduce stale-client breakage during cache rollover
- Previously failing asset URLs (`w4TTrgpo.js`, `n6zhjH-2.js`, `useAyanMy.ahlQBhWc.css`, `LoadingSpinner.BvLJy4-M.css`, `index.sKdH0kcC.css`) now return `200` with correct MIME

## Hotfix Update - 2026-04-25 09:55

- Checked Telegram auth runtime after redeploy due user-reported bot login failure
- VPS backend token `TELEGRAM_BOT_TOKEN` is present and valid (`getMe` resolves to `@iind_app_bot`)
- Bot menu button still points to web app URL `https://iindiinda.duckdns.org/`
- Cleared Laravel config/app caches to remove stale config risk
- If auth still fails, next required diagnostic is one fresh real Telegram retry timestamp to map exact `/api/auth/telegram` response path

## Hotfix Update - 2026-04-25 10:15

- Auth failure root cause narrowed to payload-format mismatch on production runtime (`/api/auth/telegram`)
- Frontend now sends `init_data` as `application/x-www-form-urlencoded` in `loginWithInitData` instead of JSON
- Full frontend bundle rebuilt and redeployed to VPS; deployed entry bundle confirms the new content-type path
- Token/config path remains healthy (`TELEGRAM_BOT_TOKEN` present + valid bot API checks)
- Next action: perform immediate Telegram bot login retry and confirm live `/api/auth/telegram` succeeds with real signed `initData`

## Hotfix Update - 2026-04-25 10:50

- Investigated local/frontend TMA report that `window.Telegram.WebApp.initData` was unavailable and AYAN stayed blocked
- Root cause confirmed in frontend bootstrap: Telegram auth relied on one-shot reads of `WebApp`/`initData`, so late Telegram context arrival could leave auth stuck in browser or failed state
- Added local fix in:
  1. `frontend/app/utils/telegram.ts`
  2. `frontend/app/composables/useTg.ts`
  3. `frontend/app/composables/useAuth.ts`
  4. `frontend/app/plugins/init.ts`
- Added unit coverage for delayed `WebApp` and delayed `initData` helper behavior
- Local verification is green (`npm run test`, `npm run typecheck`, `npm run build`), but no commit, deploy, or real Telegram retest happened in this session

## Hotfix Update - 2026-04-25 10:59

- Committed Telegram bootstrap recovery as `110c550` `fix(auth): recover delayed telegram bootstrap`
- Pushed `front/ayan` to GitHub and fast-forwarded VPS repository `/var/www/iind-app` to `110c550`
- Local, GitHub, and VPS repository states are now aligned on the same commit
- VPS repo still keeps untracked deploy directories (`frontend/public*`); live frontend runtime was not redeployed in this step

## Hotfix Update - 2026-04-25 11:12

- Rebuilt and redeployed the live frontend bundle from synced branch tip `9cc064d`
- VPS frontend static path `/var/www/iind-app/frontend/public` now serves the current build via full directory swap
- Live root HTML references current asset hashes (`BfQflojk.js`, `entry.CiIJ0BEA.css`), and core routes remain healthy
- Repository state on local, GitHub, and VPS checkout remains aligned at `9cc064d`

## Hotfix Update - 2026-04-25 12:42

- Exact root cause is now confirmed:
  1. AYAN auth gate made TMA startup sensitive to delayed Telegram bootstrap
  2. later static rebuilds accidentally baked local `frontend/.env` API base `http://89.22.226.34/api` into production HTML instead of same-origin `/api`
- Legal-pack commit was only a timing coincidence; it did not directly modify Telegram auth/bootstrap code
- Live frontend HTML is corrected again and now exposes `apiBase:"/api"`
- New prevention is shipped in source:
  - `frontend/package.json` -> `npm run build:static`
  - `frontend/scripts/build-static.mjs`
  - `frontend/scripts/verify-static-api-base.mjs`
  - `frontend/app/utils/api-base.ts`
- Latest auth hardening code commit in this session: `af93b9b` `fix(auth): harden tma bootstrap`

## Stop Point

- Current branch: `front/ayan`
- Live frontend bundle is redeployed with corrected same-origin `apiBase:"/api"`
- Latest auth hardening code commit is `af93b9b` `fix(auth): harden tma bootstrap`
- Static deploy prevention now exists in source via guarded `npm run build:static`
- Latest shipped commit is `a3591a0` `feat(ayan): expand trip/request lifecycle statuses`
- Live deployment baseline is HTTPS at `https://iindiinda.duckdns.org`
- Verified live routes (`200`):
  - `/`
  - `/ayan`
  - `/legal/ayan-terms`
  - `/api/health`
- Backend migrations are now fully applied, including lifecycle migration:
  - `2026_04_24_120000_expand_ayan_target_statuses`
- Live lifecycle API smoke is green for both trip and request target flows

## What Shipped In Lifecycle Slice

- Expanded trip/request statuses from `open|closed` to `open|matched|completed|cancelled`
- Accepting a response now moves target status to `matched`
- Owners can finalize matched targets as `completed` or `cancelled`
- Non-pending responses are now protected from deletion
- `my/responses` payload now includes linked trip/request details for richer frontend cards
- Detail pages now use unified API error message extraction helper

## Deployment Incident Note (Important)

- During VPS migration, an old partial migration state was discovered (`trips` table existed without migration row/FK)
- Recovery steps were executed on VPS:
  1. clear stuck MySQL metadata locks (including service restart)
  2. add missing `trips_driver_id_foreign` constraint
  3. register missing `2026_04_22_000002_create_trips_table` record in `migrations`
  4. run remaining migrations successfully
- Current `php artisan migrate:status` is fully green

## Verification Snapshot

- Frontend:
  - `npm run test` ✅
  - `npm run lint` ✅
  - `npm run typecheck` ✅
  - `npm run build:static` ✅
  - `npx nuxt build --preset github_pages` + `node scripts/verify-static-api-base.mjs` ✅
- Backend (VPS checkout):
  - `./vendor/bin/phpunit tests/Feature/AuthApiTest.php tests/Feature/AyanAuthTest.php tests/Feature/AyanPersistenceTest.php` ✅ (`16 tests, 127 assertions`)
- Runtime:
  - `curl -I https://iindiinda.duckdns.org/` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/legal/ayan-terms` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)
  - `curl https://iindiinda.duckdns.org/` contains `apiBase:"/api"` ✅

## Next Action

1. Decide the RF-compliant personal-data localization/hosting plan before any "legal done" claim
2. Fill final operator/controller details and formal support/requisites in the new legal texts
3. Re-review the expanded legal pack with RF counsel, then deploy it with `npm run build:static`
4. After legal-source approval, resume the pending real Telegram Mini App validation if AYAN runtime verification is still the next release gate

## API Smoke Snapshot (Live)

- Trip flow:
  - create trip -> respond -> accept => `matched`
  - owner finalize => `completed`
  - responder `my/responses` includes `trip.status = completed`
- Request flow:
  - create request -> respond -> accept => `matched`
  - owner finalize => `cancelled`
  - responder `my/responses` includes `request.status = cancelled`
- Deletion guard:
  - deleting accepted response returns `422` with `Only pending responses can be deleted`
- Cleanup:
  - synthetic smoke users/tokens were removed after verification (`COUNT = 0`)

## Next Session Prompt

```text
Read vault/master_index.md, vault/WORKFLOW.md, vault/sprint.md, and vault/resume-plan.md.
Current task: ship and validate the local Telegram bootstrap auth fix for AYAN TMA.
1) note that root cause is split between delayed Telegram bootstrap and poisoned static `apiBase`
2) use `npm run build:static` for any future VPS static frontend deploy
3) retry `/ayan` from a real Telegram Mini App and confirm signed `initData` reaches backend auth successfully
4) if TMA still fails, capture one fresh retry timestamp and correlate the exact `/api/auth/telegram` response path
5) update vault files with manual verification outcome
```

## Deployment Context

- VPS frontend static directory: `/var/www/iind-app/frontend/public`
- VPS backend repository: `/var/www/iind-app`
- Nginx tracked config: `ops/nginx/iind-vps-default.conf`
- Live domain: `https://iindiinda.duckdns.org`

## One-Line Summary

Live HTML is corrected back to same-origin `/api` and source now contains a guarded static deploy path; next safe move is one clean Telegram Mini App retry and immediate log correlation.
