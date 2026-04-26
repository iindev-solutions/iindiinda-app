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

## Legal Text Gap Review - 2026-04-26 00:10

- Reviewed all current Russian legal texts in `frontend/i18n/locales/ru.json` and captured the missing clauses that still block production-grade legal sign-off
- Do not patch the texts blindly before factual inputs exist; otherwise the docs risk making false promises about infrastructure, timelines, or verification
- Platform-wide missing facts still required before the wording pass:
  1. final operator/controller identity and requisites
  2. real support email/address for legally significant requests
  3. real hosting / processor / geography facts for personal-data handling
  4. retention periods for accounts, logs, support cases, and backups
  5. chosen age threshold and dispute / claims path
- Main document-level gaps captured for later patching:
  1. `userAgreement` still needs acceptance mechanics, age/deal-capacity wording, no-warranty/service-availability posture, content-license/moderation terms, suspension/appeal flow, and dispute handling
  2. `privacy` still needs operator disclosure, data-source detail, processor/hosting geography disclosure, concrete retention periods, rights-exercise channel, and security/backup wording
  3. `dataConsent` still needs operator identity, consent duration, explicit acceptance/revocation mechanics, consequences of withdrawal, and transfer/processor wording
  4. `support` still needs a formal email/address, requester-identification flow, and clearer split for fraud/safety/data-rights requests
  5. service rules still need sharper wording before final sign-off:
     - AYAN: stronger anti-dispatch / anti-carrier wording and explicit ride-risk allocation
     - UUS: stronger anti-employment wording and clearer regulated-category exclusions
     - TAL: stronger non-medical boundary and no-health-data wording
     - AGAL: unknown-parcel prohibition, no-insurance/default-compensation wording, and high-risk contents limits
- Safe follow-up plan: gather factual inputs first, then patch the legal texts in one focused pass

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

## UI Update - 2026-04-26 05:40

- Initial font-size no-zoom mitigation was not sufficient for the reported Telegram Mini App behavior
- AYAN create flow was simplified again to reduce slideover/WebView focus instability:
  1. replaced `UPopover + UCalendar` with a native `type="date"` input
  2. disabled `USlideover` transition inside Telegram with `:transition="!isInTelegram"`
  3. kept the prior larger-input/no-zoom styling in place
  4. made trip comment required in frontend validation/payload
  5. updated ride comment placeholders in `frontend/i18n/locales/ru.json` and `frontend/i18n/locales/sah.json`
- Runtime change shipped as `5e81817` `fix(ayan): simplify tma create form`
- Pushed `front/ayan`, fast-forwarded VPS repo, rebuilt static bundle, and redeployed live frontend hosting
- Live root HTML now references current assets `entry.7LYcEUNC.css` and `DTyp_Z4D.js`
- Real Telegram Mini App retest is now positive: user confirmed the create flow no longer shows the disruptive zoom after removing the calendar/popover layer and disabling Telegram slideover transition

## Stop Point

- Current branch: `front/ayan`
- Latest live frontend/runtime code commit is `5e81817` `fix(ayan): simplify tma create form`
- Latest branch tip contains post-deploy vault sync notes for the create-form simplification rollout
- Local, GitHub, and VPS repository states are aligned on the current branch tip
- Live frontend bundle is redeployed with collapsed-by-default service explainers on AYAN, UUS, TAL, and AGAL entry screens
- Legal docs now render via `rt()` on live build and legal navigation is reduced to the home bottom card only
- Live frontend bundle is redeployed with corrected same-origin `apiBase:"/api"`
- Latest auth hardening code commit is `af93b9b` `fix(auth): harden tma bootstrap`
- Static deploy prevention now exists in source via guarded `npm run build:static`
- Latest shipped commit is `a3591a0` `feat(ayan): expand trip/request lifecycle statuses`
- Live deployment baseline is HTTPS at `https://iindiinda.duckdns.org`
- Verified live routes (`200`):
  - `/`
  - `/ayan`
  - `/legal`
  - `/legal/ayan-terms`
  - `/legal/uus-rules`
  - `/legal/tal-rules`
  - `/legal/agal-rules`
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
  - `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`5e81817` before later vault-sync docs)
  - `curl -I https://iindiinda.duckdns.org/` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/legal/ayan-terms` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)
  - `curl https://iindiinda.duckdns.org/` contains `apiBase:"/api"` ✅
  - live root HTML references `entry.7LYcEUNC.css` and `DTyp_Z4D.js` ✅

## Next Action

1. Continue the pending real Telegram Mini App E2E validation for AYAN after the now-confirmed zoom fix
2. Manually verify the full AYAN flow on real devices:
   - create trip
   - create request
   - respond
   - accept
   - matched
   - completed
   - cancelled
   - contact reveal
   - status visibility in detail pages and `My`
3. Capture any remaining Telegram-only UX/runtime bugs from that E2E pass and patch them in focused slices
4. Keep the legal gap list parked until operator/hosting facts are ready, then resume the legal-text pass

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
Current task: continue AYAN real-device Telegram Mini App E2E after the confirmed create-form zoom fix.
1) validate create trip and create request on real devices
2) validate respond -> accept -> matched -> completed/cancelled
3) verify contact reveal and status rendering in detail pages and `My`
4) if a Telegram-only bug appears, capture exact step + timestamp + device and patch in a focused slice
5) update vault files with the manual verification outcome
```

## Deployment Context

- VPS frontend static directory: `/var/www/iind-app/frontend/public`
- VPS backend repository: `/var/www/iind-app`
- Nginx tracked config: `ops/nginx/iind-vps-default.conf`
- Live domain: `https://iindiinda.duckdns.org`

## One-Line Summary

Live AYAN now includes a simplified create form with native date input and Telegram-slideover transition disabled; zoom issue is manually confirmed fixed, so next safe move is the remaining full AYAN Telegram Mini App E2E pass.
