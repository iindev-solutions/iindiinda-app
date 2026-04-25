# Changelog — iindiinda Vault

> Format: `YYYY-MM-DD HH:MM`. New entries must be written in English.

## 2026-04-25 11:12 - Frontend Bundle Redeployed Live

### Done

- Rebuilt frontend static output from synced branch tip `9cc064d`
- Packaged `.output/public`, uploaded it to VPS, and redeployed with full directory swap (`public_new` -> `public`)
- Preserved prior hashed assets by copying `public_prev/assets` into the new `public/assets` without overwriting fresh files
- Kept VPS repository aligned at `9cc064d` while updating the live frontend bundle in `/var/www/iind-app/frontend/public`

### Verified

- `frontend: npx nuxt build --preset github_pages` ✅
- `curl -I https://iindiinda.duckdns.org/` ✅ (`200 text/html`)
- `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200 text/html`)
- `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200 application/json`)
- `curl -I https://iindiinda.duckdns.org/assets/BfQflojk.js` ✅ (`200 application/javascript`)
- `curl -I https://iindiinda.duckdns.org/assets/entry.CiIJ0BEA.css` ✅ (`200 text/css`)
- Live root HTML references current asset hashes ✅ (`LIVE_HTML_MATCH`)
- `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`9cc064d`)

### Important

- Live frontend bundle now matches current synced repository tip `9cc064d`
- Real Telegram Mini App manual verification is still required to confirm the delayed-bootstrap auth fix against signed production `initData`

## 2026-04-25 10:59 - Telegram Bootstrap Fix Sync Checkpoint

### Done

- Committed the local Telegram bootstrap auth recovery as `110c550` `fix(auth): recover delayed telegram bootstrap`
- Pushed `front/ayan` to GitHub so `origin/front/ayan` matches local HEAD
- Fast-forwarded the VPS repository at `/var/www/iind-app` to the same commit

### Verified

- `git rev-parse --short HEAD` ✅ (`110c550`)
- `git rev-parse --short origin/front/ayan` ✅ (`110c550`)
- `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`110c550`)
- Local `git status --short --branch` ✅ (aligned with origin)
- VPS `git -C /var/www/iind-app status --short --branch` ✅ (aligned; only untracked deploy dirs remain)

### Important

- Repository state is synchronized across local, GitHub, and VPS checkout
- Live frontend bundle was not redeployed in this sync step; real Telegram Mini App retest is still required after deploy

## 2026-04-25 10:50 - Telegram WebApp Bootstrap Auth Recovery

### Done

- Audited AYAN Telegram Mini App startup after report that `initData` was unavailable and `/ayan` could not open in TMA
- Root-caused frontend bootstrap race: Telegram detection/auth depended on one-shot reads of `window.Telegram.WebApp` and `initData`, so late availability could leave auth stuck or misclassified
- Added `frontend/app/utils/telegram.ts` with wait helpers for delayed `WebApp` and delayed `initData`
- Reworked `frontend/app/composables/useTg.ts` to keep reactive Telegram state snapshots and resync when Telegram context appears late
- Updated `frontend/app/plugins/init.ts` to react to delayed `WebApp`/`initData`, call `ready()`/`expand()`, and retry Telegram auto-login when context finally arrives
- Kept mock auth compatibility in `frontend/app/composables/useAuth.ts` while preserving form-urlencoded transport for real Telegram login
- Added unit coverage for delayed Telegram bootstrap helpers

### Verified

- `frontend: npm run test` ✅ (`21 tests`)
- `frontend: npm run typecheck` ✅
- `frontend: npm run build` ✅
- Final scoped review of changed Telegram/auth files ✅ (`no findings`)

### Important

- This fix is local only in current workspace; no commit or VPS deploy was performed in this session
- Remaining evidence gap is real Telegram Mini App verification after deploy, because local tooling cannot emulate signed production `initData`

## 2026-04-25 10:15 - Telegram Auth Payload Format Fix + Live Redeploy

### Done

- Root-caused multi-account Telegram auth failures to request payload format mismatch on production runtime
- Confirmed production `/api/auth/telegram` accepted form-urlencoded `init_data` but rejected JSON payload path as missing field
- Updated frontend auth login call to send `init_data` as `application/x-www-form-urlencoded` instead of JSON
- Rebuilt frontend and redeployed the full static bundle to VPS with directory-swap deploy flow
- Verified deployed entry bundle includes the auth payload-format fix

### Verified

- `frontend: npm run test -- auth` ✅ (`2 tests`)
- `frontend: npm run typecheck` ✅
- `frontend: npx nuxt build --preset github_pages` ✅
- Live bundle check: entry asset contains `application/x-www-form-urlencoded` ✅
- Live API behavior check:
  - form-urlencoded `init_data=test` reaches Telegram validation path (`Telegram user data is invalid.`) ✅
  - previous missing-field validation path reproduced for JSON probe (`The init data field is required.`) ✅

### Important

- Bot token is present and valid; this incident was not caused by token removal
- If Telegram auth still fails after this deploy, next evidence needed is one fresh in-app retry timestamp to correlate the exact runtime request/response pair

## 2026-04-25 09:55 - Telegram Auth Runtime Diagnostic

### Done

- Investigated the reported Telegram bot auth failure after the SPA full redeploy
- Verified `TELEGRAM_BOT_TOKEN` is present in VPS backend env (`.env`) and non-empty
- Verified the configured token is valid against Telegram Bot API (`getMe`) and resolves to `@iind_app_bot`
- Verified bot menu button is still configured as `web_app` pointing to `https://iindiinda.duckdns.org/`
- Cleared Laravel config/app caches on VPS (`php artisan config:clear`, `php artisan cache:clear`) to remove stale runtime config risk

### Verified

- `Bot API getMe` ✅ (`ok: true`, bot `@iind_app_bot`)
- `Bot API getChatMenuButton` ✅ (`type: web_app`, URL `https://iindiinda.duckdns.org/`)
- `php artisan config:clear` ✅
- `php artisan cache:clear` ✅
- Direct API probe with fake payload returns Telegram validation path (`Telegram user data is invalid.`), not missing-config path

### Important

- Current evidence does **not** indicate removed bot token on VPS
- If auth still fails for real Telegram sessions, next diagnostic needs one fresh real-device attempt timestamp to correlate exact `/api/auth/telegram` response for that request

## 2026-04-25 09:42 - Full SPA Rebuild + Full Bundle Redeploy

### Done

- Rebuilt the frontend SPA from scratch with `npx nuxt build --preset github_pages`
- Packed the full rebuilt `.output/public` bundle, uploaded it to VPS, and deployed by full directory swap (`public` -> `public_prev`, `public_new` -> `public`)
- Restored cache compatibility by copying previous hashed assets from `public_prev/assets` into the new `public/assets` without overwriting fresh files
- Removed temporary deployment archives from local workspace and VPS `/tmp`

### Verified

- `frontend: npx nuxt build --preset github_pages` ✅
- Full public bundle parity check (local vs VPS) ✅ (`MATCH`)
- Rebuilt asset set presence check on VPS ✅ (`LOCAL_SET_PRESENT`)
- Live checks for previously failing files now return proper MIME:
  - `w4TTrgpo.js` -> `200 application/javascript` ✅
  - `n6zhjH-2.js` -> `200 application/javascript` ✅
  - `useAyanMy.ahlQBhWc.css` -> `200 text/css` ✅
  - `LoadingSpinner.BvLJy4-M.css` -> `200 text/css` ✅
  - `index.sKdH0kcC.css` -> `200 text/css` ✅
- `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)

### Important

- Deployment is now full-bundle based, not per-file patching
- `public_prev` is intentionally kept on VPS as rollback/cache-compat safety
- Nginx `/assets/*` fallback hardening is still recommended to force `404` for missing assets instead of HTML fallback

## 2026-04-25 09:25 - Production Asset MIME Hotfix

### Done

- Investigated live Telegram/browser startup errors for blocked JS/CSS chunks and dynamic import failures
- Confirmed affected asset URLs were resolving to `text/html` instead of JS/CSS because some hashed files were missing on VPS
- Compared local build output (`frontend/.output/public/assets`) against VPS assets and identified 8 missing files
- Uploaded missing hashed assets to `/var/www/iind-app/frontend/public/assets` on VPS to restore bundle integrity

### Verified

- `curl -I https://iindiinda.duckdns.org/assets/w4TTrgpo.js` ✅ (`200`, `application/javascript`)
- `curl -I https://iindiinda.duckdns.org/assets/n6zhjH-2.js` ✅ (`200`, `application/javascript`)
- `curl -I https://iindiinda.duckdns.org/assets/useAyanMy.ahlQBhWc.css` ✅ (`200`, `text/css`)
- `curl -I https://iindiinda.duckdns.org/assets/LoadingSpinner.BvLJy4-M.css` ✅ (`200`, `text/css`)
- local-vs-VPS asset diff check ✅ (`NONE` missing files)

### Important

- Root cause is deploy artifact drift: entry bundle referenced hashed chunks that were not present in VPS assets directory
- Current Nginx SPA fallback still rewrites unknown `/assets/*` to `/index.html`; this masks missing-file issues as MIME errors
- Follow-up hardening should add explicit `/assets/` handling (`try_files $uri =404`) and cache policy split (`index.html` no-store, hashed assets immutable)

## 2026-04-24 20:15 - Final Alignment After Vault Sync Commit

### Done

- Applied final vault synchronization commit and propagated it to both origin and VPS checkout
- Re-verified repository alignment after that commit so tomorrow's resume hash is exact

### Verified

- `git rev-parse --short HEAD` ✅ (`d019d0c`)
- `git rev-parse --short origin/front/ayan` ✅ (`d019d0c`)
- `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`d019d0c`)
- `git status --short --branch` ✅ (clean, aligned)

### Important

- This is final end-of-day synchronized stop point for continuation tomorrow

## 2026-04-24 20:10 - Sync Checkpoint For Tomorrow Handoff

### Done

- Confirmed repository sync across local workspace, `origin/front/ayan`, and VPS checkout
- Pulled latest vault-only handoff commits on VPS so server-side repo matches local stop point
- Reconfirmed that lifecycle code deployment remains `a3591a0`, while latest branch tip includes vault synchronization notes

### Verified

- `git rev-parse --short HEAD` ✅ (`219387d`)
- `git rev-parse --short origin/front/ayan` ✅ (`219387d`)
- `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`219387d`)
- `git status --short --branch` ✅ (clean, aligned)
- `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)

### Important

- Branch and VPS are fully synchronized for next-day continuation
- Next work item remains manual Telegram/browser UI E2E verification for lifecycle flows

## 2026-04-24 20:00 - Lifecycle Live API Smoke Completed

### Done

- Executed a live AYAN lifecycle smoke on production API with isolated synthetic users/tokens
- Verified trip flow end-to-end: create trip -> respond -> accept -> matched -> complete
- Verified request flow end-to-end: create request -> respond -> accept -> matched -> cancel
- Verified `my/responses` now returns linked target payloads with updated lifecycle statuses
- Verified non-pending response deletion guard still enforces `422` with `Only pending responses can be deleted`
- Removed synthetic smoke users/tokens from production DB after validation to avoid polluting live data

### Verified

- Live API smoke output:
  - `tripStatusAfterAccept = matched`
  - `tripStatusAfterComplete = completed`
  - `myTripResponseTargetStatus = completed`
  - `requestStatusAfterAccept = matched`
  - `requestStatusAfterCancel = cancelled`
  - `myRequestResponseTargetStatus = cancelled`
  - `tripDeleteAcceptedStatus = 422`
- `requestDeleteAcceptedStatus = 422`
- `curl -I https://iindiinda.duckdns.org/` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)
- `SELECT COUNT(*) FROM users WHERE telegram_id IN (910000001, 910000002)` ✅ (`0`)

### Important

- Backend/contract lifecycle behavior is confirmed live through real API requests
- Telegram Mini App UI manual pass is still pending (device interaction cannot be automated from this environment)
- No synthetic smoke users remain in production after API verification cleanup

## 2026-04-24 19:50 - Lifecycle Status Expansion Deployed Live

### Done

- Shipped lifecycle slice as `a3591a0` on `front/ayan` with status model `open|matched|completed|cancelled`
- Deployed backend + frontend updates to VPS and reloaded runtime services
- Added owner-side lifecycle finalization actions in detail views (`completed` / `cancelled`) after match
- Enriched `my/responses` serialization with linked target payloads (`trip` / `request`) for better frontend cards
- Added frontend API error helper (`frontend/app/utils/api-error.ts`) and integrated it into detail flow toasts

### Verified

- `frontend: npm run test` ✅ (`9 files, 17 tests`)
- `frontend: npm run lint` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npx nuxt build --preset github_pages` ✅
- `ssh iind-vps "cd /var/www/iind-app/backend && ./vendor/bin/phpunit tests/Feature/AuthApiTest.php tests/Feature/AyanAuthTest.php tests/Feature/AyanPersistenceTest.php"` ✅ (`16 tests, 127 assertions`)
- `ssh iind-vps "cd /var/www/iind-app/backend && php artisan migrate:status"` ✅ (all AYAN migrations ran)
- `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`a3591a0`)
- `curl -I https://iindiinda.duckdns.org/` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/legal/ayan-terms` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)

### Important

- VPS migration flow needed live recovery because a historical partial migration left `trips` table created without migration record/FK
- Recovery was completed safely (clear metadata locks, restore missing FK, register missing migration row, apply pending migrations)
- Manual Telegram Mini App E2E verification is still pending and should be done next

## 2026-04-24 19:00 - Legal Pack Deployed Live

### Done

- Isolated legal-only frontend changes from the mixed legal+lifecycle workspace and committed them as `f13f6b6` on `front/ayan`
- Pushed `front/ayan` to origin and fast-forwarded VPS repo at `/var/www/iind-app` to the same commit
- Rebuilt frontend static output with `npx nuxt build --preset github_pages`
- Uploaded `.output/public` bundle to VPS frontend path `/var/www/iind-app/frontend/public`, set owner to `www-data`, and reloaded Nginx
- Kept lifecycle expansion slice local and uncommitted (backend status migration/controllers/tests and frontend lifecycle UI/status updates)

### Verified

- `frontend: npm run test` ✅ (`9 files, 17 tests`)
- `frontend: npm run lint` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npx nuxt build --preset github_pages` ✅
- `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`f13f6b6`)
- `curl -I https://iindiinda.duckdns.org/` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/legal/ayan-terms` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/legal/privacy` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/legal/ayan-safety` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)

### Important

- Legal pages/links are now live in production on HTTPS
- Lifecycle status expansion remains in-progress locally and should be shipped as a separate, safe follow-up slice

## 2026-04-24 18:39 - Vault Handoff Refresh Before New Session

### Done

- Re-audited current branch state (`front/ayan`) and confirmed head alignment with `origin/front/ayan` at `e150493`
- Captured exact split between local legal-pack work and local lifecycle-expansion work in `vault/resume-plan.md`
- Rewrote `vault/sprint.md` and `vault/resume-plan.md` to make legal-only ship the explicit next action
- Updated `vault/CODE_MAP.md` notes so new legal files and in-progress lifecycle files are discoverable from vault docs
- Appended this handoff update and a new session note for clean restart

### Verified

- `git status --short --branch` ✅ (branch aligned, mixed local diff clearly visible)
- `git log --oneline --decorate -12` ✅ (latest committed state still `e150493`)
- Mandatory vault files were re-read in protocol order before update ✅

### Important

- Legal pack is implemented locally but still not committed, pushed, or deployed
- Lifecycle expansion files remain in-progress and must not be accidentally included in legal-only commit/deploy
- Next session should execute legal-only staging and deployment first, then continue lifecycle work as a separate slice

## 2026-04-24 11:56 — Response Status UX + iPhone Zoom Fix Live

### Done

- Added detail-page awareness of the current user's existing response by loading `/ayan/my/responses` for non-owner views
- Replaced the repeat response form with a status card when the user has already responded
- Exposed accepted/pending/rejected status on detail pages and in the `My` tab response cards
- Added navigation from `My` responses back to the related trip/request detail page
- Applied `fixed` sizing to slideover form controls so iPhone focus no longer auto-zooms the create form fields
- Pushed commit `09c654b` `feat(ayan): show response status and fix zoom`
- Fast-forwarded the VPS repo to `09c654b` and deployed the rebuilt SPA bundle to `https://iindiinda.duckdns.org`

### Verified

- `frontend: npm run test` ✅ (`7 files, 15 tests`)
- `frontend: npm run lint` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npx nuxt build --preset github_pages` ✅
- `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`09c654b`)
- `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)
- deployed HTML still contains `apiBase:"/api"` and `devInitData:""` ✅

### Important

- Users who already responded should now see their current status instead of a second response form
- Rejected responses are visible in both the detail view and `My` tab
- The live bundle was uploaded to `frontend/public/`, so the VPS repo still shows that directory as intentionally untracked deployment output

## 2026-04-24 12:36 — AYAN Entry Polish Live

### Done

- Added always-visible UI back button on AYAN detail pages while keeping Telegram native back button support
- Replaced the trip price stepper with a normal text price field plus trailing `₽`
- Switched AYAN date selection in the create slideover to a Nuxt UI calendar popover
- Disabled past dates in the calendar and added frontend validation for past-date selection
- Added small helper coverage for price parsing/sanitizing and back-button display logic
- Pushed commit `87a4815` `feat(ayan): polish entry form and detail nav`
- Fast-forwarded the VPS repo to `87a4815` and uploaded the rebuilt SPA bundle to the HTTPS deployment

### Verified

- `frontend: npm run test` ✅ (`6 files, 13 tests`)
- `frontend: npm run lint` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npx nuxt build --preset github_pages` ✅
- `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`87a4815`)
- `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)
- deployed HTML still contains `apiBase:"/api"` and `devInitData:""` ✅

### Important

- The polished create-form and detail-nav UX is now live on the HTTPS VPS deployment
- VPS repo remains intentionally dirty only because `frontend/public/` is the uploaded static bundle and is not tracked by git

## 2026-04-24 12:05 — TMA Auth Root Cause: Missing Bot Token

### Done

- Investigated the Telegram Mini App login failure against the real VPS runtime instead of assuming role-switch logic was broken
- Confirmed the deployed frontend no longer ships `devInitData:test` and now calls `/api` over HTTPS correctly
- Found the real live blocker: `TELEGRAM_BOT_TOKEN` was missing from `backend/.env` on VPS
- Added the provided bot token only on the VPS `.env` file and did not write it into the repository
- Cleared Laravel caches on VPS after updating the environment

### Verified

- VPS env check: `TELEGRAM_BOT_TOKEN` present ✅
- `php artisan optimize:clear` on VPS ✅
- `POST /api/auth/telegram` with fake payload now returns `Telegram user data is invalid.` instead of `Telegram auth is not configured.` ✅
- `https://iindiinda.duckdns.org/api/health` ✅ (`200`)

### Important

- The current TMA auth failure was not caused by role switching itself
- The real cause was missing backend bot token, which prevented hash validation of Telegram `initData`
- End-to-end TMA still needs manual retest from the real bot/Mini App after this server-side env fix

## 2026-04-23 23:53 — Auth Gate + Production Fallback Cleanup

### Done

- Investigated the real auth flow gap instead of treating role switching as the main bug
- Confirmed `switch-role` itself is protected by Sanctum and the real issue was incomplete auth UX outside Telegram Mini App
- Added frontend auth helpers for access-state derivation and localhost-only dev fallback usage
- Added AYAN access-state UI so unauthenticated browser users see an explicit Telegram-only message instead of half-working AYAN screens
- Added AYAN auth-failed state so Telegram users see a retryable auth message instead of silent broken behavior
- Stopped production frontend usage of `devInitData=test` by removing it from `frontend/.env` and redeploying the SPA bundle
- Rebuilt and redeployed the VPS frontend with safe runtime config:
  - `apiBase: "/api"`
  - `devInitData: ""`
- Updated VPS Nginx SPA fallback to use a named location and stable HTTPS server blocks

### Verified

- `frontend: npm run test` ✅ (`4 files, 9 tests`)
- `frontend: npm run lint` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npx nuxt build --preset github_pages` ✅
- `curl https://iindiinda.duckdns.org/` HTML contains `apiBase:"/api"` ✅
- `curl https://iindiinda.duckdns.org/` HTML contains `devInitData:""` ✅
- `curl -I https://iindiinda.duckdns.org/` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)
- `curl -s -o NUL -w "%{http_code}" https://iindiinda.duckdns.org/api/user` ✅ (`401` guest blocked)

### Important

- The site no longer tries mixed-content API calls to `http://89.22.226.34/api`
- Guest browser users should now be blocked at the AYAN UI level instead of discovering the auth gap by broken actions
- TMA/manual live verification is still required to confirm the real Telegram `initData` path behaves correctly with the new gate UX

## 2026-04-23 19:31 — DuckDNS + HTTPS Live

### Done

- Bound `iindiinda.duckdns.org` to VPS IP `89.22.226.34`
- Updated VPS Nginx `server_name` to `iindiinda.duckdns.org`
- Installed `certbot` and `python3-certbot-nginx` on VPS
- Issued a real Let's Encrypt certificate for `iindiinda.duckdns.org`
- Enabled HTTP -> HTTPS redirect through Certbot-managed Nginx config
- Added DuckDNS updater script on VPS at `/opt/duckdns/update.sh`
- Added root crontab entry to refresh the DuckDNS record every 5 minutes

### Verified

- `nslookup iindiinda.duckdns.org` ✅ (`89.22.226.34`)
- `curl -I http://iindiinda.duckdns.org/` ✅ (`301` -> HTTPS)
- `curl -I https://iindiinda.duckdns.org/` ✅ (`200`)
- `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)
- `certbot --nginx ... -d iindiinda.duckdns.org` ✅
- `/opt/duckdns/update.sh` executes successfully ✅

### Important

- Telegram-ready hostname is now available: `https://iindiinda.duckdns.org`
- DuckDNS token is stored only on VPS inside `/opt/duckdns/update.sh`; it was not written into repo code
- Certbot dry-run check initially slept because of random renewal delay; live cert issuance itself succeeded and HTTPS is already working

## 2026-04-23 17:56 — Push + Backend Deploy + VPS HTTP Frontend

### Done

- Pushed `front/ayan` to origin with the AYAN role-switch, past-item UX, vault, and ops changes
- Fast-forwarded the VPS repo to the pushed branch tip
- Deployed the backend part on VPS and re-ran focused backend feature tests on the real checkout
- Fixed one failing backend test expectation so `my/*` keeps full history instead of only upcoming items
- Kept the frontend static bundle served from VPS root over HTTP and preserved `/api/*` backend routing

### Verified

- `git push origin front/ayan` ✅
- `ssh iind-vps "git -C /var/www/iind-app pull --ff-only origin front/ayan"` ✅
- `ssh iind-vps "cd /var/www/iind-app/backend && ./vendor/bin/phpunit tests/Feature/AuthApiTest.php tests/Feature/AyanAuthTest.php tests/Feature/AyanPersistenceTest.php"` ✅ (`15 tests, 112 assertions`)
- `curl -I http://89.22.226.34/` ✅ (`200`)
- `curl -I http://89.22.226.34/ayan` ✅ (`200`)
- `curl -I http://89.22.226.34/api/health` ✅ (`200`)

### Important

- VPS now serves frontend SPA and backend API from one machine over HTTP
- Backend past-item filtering/guards are deployed on VPS together with the frontend role switcher
- Added backend test coverage for rejecting accept/reject on already-past trip/request targets
- Trusted HTTPS is still blocked by infrastructure, not code: a hostname/domain must exist before issuing a real TLS cert

## 2026-04-23 17:37 — AYAN Role Switch UI + VPS SPA HTTP

### Done

- Added AYAN role switch UI in the page header so the user can toggle between `passenger` and `driver`
- Reused the existing backend endpoint `POST /api/user/switch-role` through the frontend auth composable
- Added a small AYAN role utility and unit coverage for role-to-create-mode mapping
- Kept the earlier past-item/free-price slice in the same local working set
- Added a repo-tracked Nginx config at `ops/nginx/iind-vps-default.conf` for serving the SPA from VPS root while keeping `/api/*` on Laravel
- Uploaded the built frontend static output to `/var/www/iind-app/frontend/public`
- Switched the VPS Nginx default site so:
  - `/` serves the frontend SPA
  - `/ayan` falls back to SPA entry
  - `/api/*` still serves the backend

### Verified

- `frontend: npm run test` ✅ (`3 files, 7 tests`)
- `frontend: npm run lint` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npx nuxt build --preset github_pages` ✅
- `curl -I http://89.22.226.34/` ✅ (`200`)
- `curl -I http://89.22.226.34/ayan` ✅ (`200`)
- `curl -I http://89.22.226.34/api/health` ✅ (`200`)
- `nginx -t` on VPS ✅

### Important

- The VPS now serves the frontend over plain HTTP from the same machine as the backend
- Trusted HTTPS is still not available because there is no hostname/domain attached to the server yet
- With only a raw VPS IP, practical next options are:
  - attach a real domain and issue a normal TLS cert, or
  - attach a free hostname service you control, then issue TLS for that hostname
- Raw-IP HTTPS without a proper hostname is not a good Telegram-ready deployment path

## 2026-04-23 17:15 — Past Item Logic + Free Price UX

### Done

- Added frontend price formatting so `0` is rendered as a localized free label instead of `0 ₽`
- Added frontend past-item detection helper for AYAN date/time values
- Added AYAN past badges on My cards and detail pages while keeping past items hidden from the public feed design
- Disabled response actions in frontend for past trip/request detail views
- Added backend upcoming-feed filtering so public `trips` and `requests` indexes only return still-upcoming open items
- Added backend guards so expired-but-still-open trips/requests reject new responses and accept/reject actions
- Added regression tests for formatter behavior and backend feed/expired-response behavior in local code

### Verified

- `frontend: npm run test` ✅ (`2 files, 5 tests`)
- `frontend: npm run lint` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npx nuxt build --preset github_pages` ✅
- `vps isolated copy: php -l` on changed backend files ✅
- `vps isolated copy: php artisan route:list --path=api/ayan` ✅

### Important

- Backend runtime behavior changes are implemented in local code, but full backend feature-test execution for the modified code was not reproducible in the isolated VPS copy because:
  - copied MySQL test runs collide with existing server test tables
  - SQLite fallback is unavailable on the VPS PHP build (`could not find driver`)
- Live VPS checkout was not modified for this slice after the clean sync; changes remain local in the workspace
- If we want frontend on VPS too, the simplest path is static deploy via Nginx serving `frontend/.output/public`; this is easier to control than `gh-pages` and fits later same-domain API hosting

## 2026-04-23 16:12 — VPS Synced Cleanly + Backend Hardening Verified

### Done

- Preserved the previous dirty VPS backend state before sync:
  - `/root/iind-backups/backend-dirty-20260423.patch`
  - `/root/iind-backups/backend-dirty-20260423.status`
  - `/root/iind-backups/backend-untracked-20260423.list`
  - `/root/iind-backups/backend-untracked-20260423.tgz`
- Used `git stash push -u` on VPS and synchronized `/var/www/iind-app/backend` to `origin/front/ayan`
- Confirmed VPS backend is now at clean branch tip `1fd837f`
- Re-ran backend verification on the clean deployed checkout
- Built frontend with `npx nuxt build --preset github_pages`
- Audited the current driver/passenger role logic across frontend and backend

### Verified

- `ssh iind-vps "cd /var/www/iind-app/backend && git stash push -u -m pre-sync-20260423 && git fetch origin front/ayan && git reset --hard origin/front/ayan && git status --short --branch"` ✅
- `ssh iind-vps "cd /var/www/iind-app/backend && ./vendor/bin/phpunit tests/Feature/AuthApiTest.php tests/Feature/AyanAuthTest.php tests/Feature/AyanPersistenceTest.php"` ✅ (`13 tests, 94 assertions`)
- `ssh iind-vps "cd /var/www/iind-app/backend && php artisan route:list --path=api/ayan"` ✅
- `curl -I http://89.22.226.34/api/health` ✅
- `frontend: npx nuxt build --preset github_pages` ✅

### Important

- The committed AYAN auth/authorization hardening is now verified on a clean synchronized VPS checkout
- The previous VPS dirty state is still recoverable via the backup files and `stash@{0}` (`pre-sync-20260423`)
- Current product gap: role switching exists in backend (`POST /api/user/switch-role`) and in frontend composable (`useAuth().switchRole`), but there is no frontend UI that actually calls it yet
- Because of that gap, a new user logs in as `passenger` by default and cannot become `driver` from the current UI without a manual API call or new role-switch screen

## 2026-04-23 15:52 — VPS Audit: SSH Restored, Deploy Drift Confirmed

### Done

- Re-ran the project stop-point audit against `vault/`, the local frontend workspace, and the VPS backend runtime
- Confirmed local frontend verification is still green: `npm run typecheck`, `npm run lint`, `npm run test`
- Confirmed local backend execution is still unavailable because `php` is not installed in this environment
- Confirmed `ssh iind-vps` works again from this machine
- Confirmed VPS backend runtime is live: `/api/health` returns `200`, AYAN routes are registered, and focused backend feature tests pass on the server
- Confirmed the VPS checkout is still on `2ef7fb6` and is dirty with backend runtime changes, while local `front/ayan` is 5 commits ahead

### Verified

- `git status --short --branch` ✅
- `git log --oneline --decorate -8` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npm run lint` ✅
- `frontend: npm run test` ✅
- `php -v` ❌ (`php` not installed locally)
- `ssh -o BatchMode=yes -o ConnectTimeout=10 iind-vps exit` ✅
- `ssh iind-vps "git -C /var/www/iind-app/backend status --short --branch"` ✅
- `ssh iind-vps "git -C /var/www/iind-app/backend log --oneline --decorate -5"` ✅
- `ssh iind-vps "cd /var/www/iind-app/backend; ./vendor/bin/phpunit tests/Feature/AuthApiTest.php tests/Feature/AyanAuthTest.php tests/Feature/AyanPersistenceTest.php"` ✅ (`6 tests, 69 assertions`)
- `ssh iind-vps "cd /var/www/iind-app/backend; php artisan route:list --path=api/ayan"` ✅
- `curl -I http://89.22.226.34/api/health` ✅

### Important

- The old blocker `ssh iind-vps` is no longer active
- The real blocker is now deployment drift on VPS: the server checkout is dirty and still pinned to `2ef7fb6`, so the committed hardening slice `755f7c6` is not yet deployed as clean git history
- The VPS dirty diff shows runtime and persistence work is present there, but `AuthController` still lacks the stricter signed Telegram `initData` verification from the later hardening commit
- The next deployment step must preserve or intentionally discard the dirty VPS edits before any sync to `origin/front/ayan`

## 2026-04-23 11:20 — Empty Template Added

### Done

- Added `empty-template/` inside the repository as a new starter project template
- Kept the template minimal on purpose: starter frontend files, starter backend files, and a full vault-first documentation structure
- Made `vault/` the explicit heart of the template through:
  - `empty-template/AGENTS.md`
  - `empty-template/vault/master_index.md`
  - `empty-template/vault/WORKFLOW.md`
  - `empty-template/vault/sprint.md`
  - `empty-template/vault/resume-plan.md`
  - `empty-template/vault/CODE_MAP.md`
  - `empty-template/vault/logs/changelog.md`
  - `empty-template/vault/SESSION_LEDGER.md`

### Verified

- `empty-template/` directory exists
- starter `frontend/package.json` exists
- starter `backend/composer.json` exists
- starter `vault/` entry docs exist and are written in English

### Important

- The backend side of `empty-template/` is intentionally a starter scaffold, not a full generated Laravel runtime
- The template is designed to be adapted into a new project while preserving the vault-first workflow

## 2026-04-23 10:55 — Vault English Standard

### Done

- Rewrote the active vault entry docs in English
- Added `vault/WORKFLOW.md` as the mandatory vault usage protocol
- Added `vault/SESSION_LEDGER.md` as a short session memory layer
- Updated `AGENTS.md` so future sessions must read and update `vault/`

### Important

- `vault/` is now explicitly treated as the canonical project memory system
- New vault content must be written in English
- Older historical pages outside the active entry set may still contain mixed language and should be translated when touched

---

## 2026-04-23 — AYAN Auth Hardening + Push

### Что сделано
- Добавлен и закоммичен hardening slice `755f7c6` `fix(ayan): enforce auth and response rules`
- `git push origin front/ayan` выполнен успешно
- Backend локально усилен:
  - signed Telegram `initData` parsing вместо простого blind parse
  - `init_data=test` только для `local/testing`
  - role/owner enforcement для AYAN create/respond/list responses
  - duplicate/closed response guards
  - single accepted response guard
- Frontend AYAN выровнен под новые backend rules:
  - role-aware create UI
  - non-owner detail pages больше не вызывают owner-only `/responses`
- `vault/wiki/services/ayan/api-contract.md` обновлён под live backend surface

### Verified
- `git push origin front/ayan` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npm run lint` ✅
- `frontend: npm run test` ✅

### Blocked
- `ssh iind-vps` / `ssh root@89.22.226.34` ❌
- Симптом: SSH handshake проходит, затем сервер закрывает соединение: `Connection closed by 89.22.226.34 port 22`
- Из-за этого не выполнены:
  - `git -C /var/www/iind-app/backend pull --ff-only`
  - backend phpunit на VPS для нового hardening commit

### Next
- Восстановить SSH доступ к `iind-vps`
- На VPS сделать `git pull` в `/var/www/iind-app/backend`
- На VPS прогнать `AuthApiTest`, `AyanAuthTest`, `AyanPersistenceTest`
- После remote green обновить `vault` ещё раз и зафиксировать deploy verification commit

## 2026-04-23 — GitHub Pages Live + AYAN VPS Smoke

### Что сделано
- Повторно проверен GitHub Pages deploy для `gh-pages`
- Подтверждено, что `https://iindev-solutions.github.io/iindiinda-app/` уже live
- Прогнан direct smoke против VPS backend через реальные AYAN endpoints с двумя synthetic Telegram payload users
- Обновлены `vault/sprint.md`, `vault/resume-plan.md`, `vault/CODE_MAP.md` под новый stop point

### Verified
- `HEAD https://iindev-solutions.github.io/iindiinda-app/` → `200` ✅
- `HEAD https://iindev-solutions.github.io/iindiinda-app/ayan` → `200` ✅
- rebased asset `/iindiinda-app/assets/entry.DKuJVqy4.css` → `200` ✅
- `POST /api/auth/telegram` with synthetic `init_data` for 2 users ✅
- `POST /api/ayan/trips` ✅
- `POST /api/ayan/requests` ✅
- `POST /api/ayan/trips/{id}/responses` ✅
- `POST /api/ayan/requests/{id}/responses` ✅
- `PATCH /api/ayan/responses/{id}` accept flow ✅
- `GET /api/ayan/my/trips`, `/my/requests`, `/my/responses` ✅

### Важно
- GitHub Pages propagation/source blocker больше не актуален: deploy уже live
- Main next step сместился с deploy verification на реальный browser UI flow против VPS backend
- В generated HTML публичный ключ `devInitData` всё ещё сериализуется как пустая строка; deploy build нельзя собирать с непустым `NUXT_PUBLIC_DEV_INIT_DATA`

## 2026-04-22 — GitHub Pages Deploy Attempt

### Что сделано
- Собран static frontend output из `frontend/` через `npx nuxt build --preset github_pages`
- Выявлен deploy nuance: build с `NUXT_APP_BASE_URL=/iindiinda-app/` ломает Nuxt prerender, потому что crawler идёт в `/`, а Nitro mount'ит app под repo base path
- Рабочий временный flow: build с `NUXT_APP_BASE_URL=/`, затем rebase generated HTML/CSS под repo path `/iindiinda-app/`
- Из generated HTML убран публичный `devInitData:"test"` fallback для deploy build
- Содержимое `frontend/.output/public` опубликовано в новую ветку `gh-pages` отдельным temp-repo commit'ом `bff6aa5`

### Verified
- `npx nuxt build --preset github_pages` with `NUXT_APP_BASE_URL=/` ✅
- rebased output содержит `/iindiinda-app/assets/*` и `app.baseURL:"/iindiinda-app/"` ✅
- `git push -u origin gh-pages` ✅

### Важно
- Expected URL: `https://iindev-solutions.github.io/iindiinda-app/`
- На момент последней проверки URL ещё отвечал `404`
- Причина вне локального build pipeline: либо GitHub Pages source ещё не включён на repo, либо deploy не успел подняться

## 2026-04-22 — Frontend AYAN Real API Switch

### Что сделано
- `frontend/app/config/api.config.ts`: `USE_MOCK_API = false`
- `frontend/useAuth.ts` переведён в TMA-first поведение: browser mode больше не пытается автоматически запускать старый Telegram OAuth flow без backend support
- `frontend/nuxt.config.ts` теперь знает `public.telegramBotId`
- `.env.example` дополнен `NUXT_PUBLIC_TELEGRAM_BOT_ID`
- `vault/sprint.md`, `vault/resume-plan.md`, `vault/CODE_MAP.md` обновлены под новый stop point

### Verified
- `npm run typecheck` ✅
- `npm run lint` ✅

### Важно
- AYAN composables уже ходят в real API
- Browser auth пока intentionally урезан до TMA-only path до появления real OAuth / Telegram verification end-to-end
- Следующий шаг: пройти UI flow против VPS backend и затем закоммитить frontend integration пакет

### Follow-up
- Local frontend dev against VPS now uses `frontend/.env`:
  - `NUXT_PUBLIC_API_BASE=http://89.22.226.34/api`
  - optional `NUXT_PUBLIC_DEV_INIT_DATA=test` для browser-only smoke login без Telegram

## 2026-04-22 — VPS Backend Bring-Up + AYAN Persistence

### Что сделано
- Поднят реальный Laravel runtime для `backend/` на VPS (`/var/www/iind-app/backend`) под `Nginx + PHP-FPM + MySQL`
- Восстановлен Laravel base в `backend/`: `artisan`, `composer.json`, `bootstrap/`, `config/`, `routes/console.php`, `resources/`, `tests/`, `storage/`
- Настроен Nginx на `backend/public`, health endpoint начал отвечать по HTTP
- Установлен `laravel/sanctum`, добавлена миграция `personal_access_tokens`
- `AuthController` переведён с `mock_token_*` на реальный Sanctum token issuance
- `UserController` переведён на authenticated user вместо hardcoded mock payload
- `TripController`, `RequestController`, `ResponseController`, `MyController` переведены с sample arrays на MySQL persistence
- Добавлен `ForceJsonResponse` middleware, чтобы guest protected API давал JSON `401`, а не HTML redirect / `Route [login] not defined`
- Исправлены backend migrations под реальный Laravel/MySQL runtime:
  - `unsignedDecimal()` → `decimal()` в `users`
  - убран DB-level `CHECK` constraint из `responses`, несовместимый с текущим MySQL FK setup
- Добавлены backend feature tests:
  - `backend/tests/Feature/AuthApiTest.php`
  - `backend/tests/Feature/AyanAuthTest.php`
  - `backend/tests/Feature/AyanPersistenceTest.php`
- Обновлены `vault/sprint.md`, `vault/resume-plan.md`, `vault/CODE_MAP.md` под новый stop point

### Verified
- `./vendor/bin/phpunit tests/Feature/AuthApiTest.php tests/Feature/AyanAuthTest.php /var/www/iind-app/backend/tests/Feature/AyanPersistenceTest.php` ✅ (`6 tests, 69 assertions`)
- `curl http://89.22.226.34/api/health` ✅ (`200`)
- `curl http://89.22.226.34/api/ayan/trips` ✅ (`401` JSON guest auth)
- `POST /api/auth/telegram` → real Sanctum token ✅
- `GET /api/user` with bearer token ✅

### Важно
- Telegram `initData` verification пока ещё не production-grade: есть stub `init_data = test` + простой parse payload
- Frontend всё ещё на `USE_MOCK_API = true`, integration пакет ещё не начат
- Изменения пока не зафиксированы git commit'ом; VPS и локальный repo синхронизированы файлово, но branch ещё dirty

### Next
- Закоммитить и запушить Laravel runtime + backend fixes
- Переключить фронт `mock → real` и пройти AYAN flow против VPS backend
- Отдельным пакетом закрыть настоящую Telegram `initData` verification

## 2026-04-22 — Deep Audit + Resume Plan

### Что сделано
- Проведён глубокий аудит `vault`, frontend и backend для восстановления stop point
- Создан `vault/resume-plan.md` — единая точка входа: где остановились, что блокирует, что делать дальше
- Обновлён `vault/sprint.md` — добавлены `Resume Point`, реальные блокеры и список resume files
- Обновлён `vault/master_index.md` — добавлена ссылка на resume-plan, исправлен счётчик задач спринта
- Обновлён `vault/CODE_MAP.md` — добавлен `AppBottomNav.vue`, зафиксирован факт что backend всё ещё на old `orders` API, а `app.vue` loader overlay отключён

### Ключевой вывод
- Мы остановились после почти готового AYAN frontend на mock API
- Следующий реальный этап: заменить backend `/ayan/orders/*` на contract-aligned AYAN API (`trips`, `requests`, `responses`, `my/*`)

### Verified
- Аудит docs/code sync ✅

---

## 2026-04-22 — Vitest Setup Baseline

### Что сделано
- Завершён начатый setup `vitest`
- Добавлены scripts: `test`, `test:watch`
- Добавлен `frontend/vitest.config.ts`
- Добавлен smoke test `frontend/tests/unit/validators.test.ts`
- Обновлены `vault/resume-plan.md`, `vault/sprint.md`, `vault/CODE_MAP.md` под новый stop point
- Текущий уровень готовности: baseline для plain TS unit tests, не полный Nuxt/composable test harness

### Verified
- `npm run test` ✅
- `npm run typecheck` ✅
- `npm run lint -- tests/unit/validators.test.ts vitest.config.ts` ✅ (по факту запускает `eslint .` в frontend)

---

## 2026-04-22 — Backend AYAN Contract Skeleton

### Что сделано
- Добавлен базовый Laravel-style skeleton под новый AYAN contract
- Добавлены модели: `User`, `Trip`, `AyanRequest`, `AyanResponse`
- Добавлены миграции: `users`, `trips`, `requests`, `responses`
- Добавлены новые controllers: `TripController`, `RequestController`, `ResponseController`, `MyController`
- `backend/routes/api.php` переведён с old `/ayan/orders/*` на новый набор `trips / requests / responses / my/*`
- Исправлены namespaces/imports в `AuthController`, `UserController`, добавлен базовый `Controller.php`

### Важно
- Это пока **contract-aligned skeleton**, не подтверждённый рабочим Laravel runtime
- В текущей среде нет `php`, `composer`, `docker`, поэтому backend нельзя было прогнать или промигрировать

### Next
- Поднять реальный Laravel runtime
- Прогнать миграции
- Заменить mock payloads на persistence и реальную auth-логику

### Docs
- Добавлен `vault/wiki/services/ayan/backend-bringup.md` — пошаговый runtime checklist для первого реального запуска backend

## 2026-04-19 — AYAN Slideover + Color Fix

### Slideover: Merge Create Forms

**Проблема:** Две отдельные страницы (`create-trip.vue`, `create-request.vue`) с почти идентичным кодом. Две кнопки на ленте. Навигация на отдельную страницу = задержка.

**Решение:**
- Создан `AyanCreateSlideover.vue` — единый bottom-slideover с pill-табами (Поездка/Запрос)
- `side="bottom"` + `rounded-t-2xl` + `max-h-[85dvh]` — мобильный sheet
- Общие поля: откуда, куда, дата, время
- `formType === 'trip'` → места + цена + комментарий
- `formType === 'request'` → комментарий (description)
- После сабмита → slideover закрывается, форма сбрасывается
- Одна кнопка на ленте вместо двух → открывает slideover
- Удалены `create-trip.vue`, `create-request.vue`

### Color Fix: cyan/gray → primary/neutral

**Проблема:** `color="cyan"` / `color="gray"` — не валидные Nuxt UI v4 prop values. TS ошибки + красная ui.vue страница.

**Решение:**
- `color="cyan"` → `color="primary"` (primary=cyan в app.config)
- `color="gray"` → `color="neutral"` (neutral=gray в app.config)
- `color="cyan"` (rejected badge) → `color="error"` (семантически верно)
- `color="cyan"` (progress) → `color="success"` (семантически верно)
- Затронуто: `BackButton.vue`, `ErrorMessage.vue`, `ui.vue`
- **typecheck + lint: 0 ошибок** (впервые чисто)

### i18n
- Добавлены `ayan.create.ride/request/from/to/date/time` (ru + sah)

### Verified
- typecheck ✅ lint ✅

---

## 2026-04-19 — Forms, Validation, Performance

### Forms: Error State + Layout (create-request, create-trip)

**Проблема:** UFormField не показывал error-состояние (красный ring) на инпутах. Причина — `ui.theme.colors: ['cyan', 'gray']` в nuxt.config.ts ограничивала палитру Nuxt UI, убирая `error`/`warning`/`success`/`info`/`secondary` цвета. FormField передаёт `color="error"` инпуту, но без этих цветов в теме — ring не применялся.

**Фикс:**
- Убрана ограниченная палитра `ui.theme.colors` из `nuxt.config.ts` (закомментирована)
- Удалён дубликат `frontend/app.config.ts` (конфликтовал с `frontend/app/app.config.ts`)
- Все UI-оверрайды в одном файле: `frontend/app/app.config.ts` (colors: primary=cyan, neutral=gray)
- Формы: `eager-validation` на обязательных полях — ошибка видна сразу после первого взаимодействия
- Формы: `class="w-full"` на UInput/UTextarea/UInputNumber — инпуты растягиваются на всю ширину
- Формы: `:label` на UFormField — подписи полей вместо placeholder-only
- Формы: `FormError` + `FormSubmitEvent` типы из `@nuxt/ui`
- Формы: дата/время и места/цена — `grid grid-cols-2 gap-3`
- i18n: добавлены `commentPlaceholder`, `time` ключи (ru + sah)

### Performance: Nuxt 4 Best Practices

**Что сделано:**

1. **`useLoadingIndicator().isLoading`** → overlay спиннер в `app.vue` с `backdrop-blur-sm` + `<Transition name="loader-fade">`. Показывается при навигации между страницами, пока данные грузятся.

2. **`useLazyAsyncData`** вместо `await useAsyncData` на всех AYAN страницах (`index.vue`, `trip/[id].vue`, `request/[id].vue`). Навигация мгновенная, данные подгружаются после рендера.

3. **`{ deep: false }`** в `useLazyAsyncData` на `index.vue` — списки не глубоко реактивные (экономия на proxy).

4. **`definePageMeta({ lazy: true })`** на AYAN дочерних страницах — бандлы страниц подгружаются lazy, не блокируют переход.

5. **`prefetchOn: { visibility: true, interaction: true }`** в `experimental.defaults.nuxtLink` — NuxtLink префетчит при видимости/взаимодействии, не грузит всё заранее.

6. **`pageTransition` убран** — конфликтует с `lazy: true` (Vue warning: "non-element root node"). Overlay loader обеспечивает визуальный фидбек вместо page transition.

**Слои загрузки теперь:**
- `spa-loader.html` — первый холодный рендер (пока JS бандл грузится)
- `NuxtLoadingIndicator` — тонкая полоска сверху при навигации
- `useLoadingIndicator().isLoading` → overlay спиннер (полноэкранный)
- `useLazyAsyncData` — данные не блокируют навигацию
- `lazy: true` — бандлы подгружаются параллельно

### CSS
- `main.css`: `.loader-fade-enter/leave` — 200ms fade для overlay
- `main.css`: `.page-enter/leave` удалены (pageTransition убран)

### TS (pre-existing)
- `color="cyan"` / `color="gray"` TS errors в BackButton, ErrorMessage, ui.vue — Nuxt UI не включает кастомные цвета в union type. Рантайм работает. TODO: исправить типы.

### Verified
- lint ✅ (typecheck: pre-existing cyan/gray TS errors)

---

## 2026-04-19 — Task 1.3: Frontend AYAN Structure ✅

### Added
- `services/ayan/app/types/ayan.ts` — типы AyanTrip, AyanRequest, AyanResponse, DTO (по API контракту)
- `services/ayan/app/config/ayanMock.ts` — mock генерация trips/requests/responses + useState store для поиска по ID
- `services/ayan/app/composables/useAyanTrips.ts` — CRUD поездок через useAPI (fetchTrips, fetchTrip, createTrip, updateTrip)
- `services/ayan/app/composables/useAyanRequests.ts` — CRUD запросов (fetchRequests, fetchRequest, createRequest)
- `services/ayan/app/composables/useAyanResponses.ts` — отклики (fetch/create/cancel)
- `services/ayan/app/composables/useAyanMy.ts` — мои данные
- `services/ayan/app/pages/ayan.vue` — parent wrapper
- `services/ayan/app/pages/ayan/index.vue` — лента поездок/запросов/мои с табами
- `services/ayan/app/pages/ayan/create-trip.vue` — форма создания поездки
- `services/ayan/app/pages/ayan/create-request.vue` — форма создания запроса
- `services/ayan/app/pages/ayan/trip/[id].vue` — детали поездки + отклик
- `services/ayan/app/pages/ayan/request/[id].vue` — детали запроса + отклик
- i18n: `ayan.validation.*`, `ayan.status.*`, `ayan.responses` (ru + sah)

### Changed (audit fixes)
- index.vue: UTabs `model-value` static → `:model-value="activeTab"` (reactive)
- index.vue: `onMounted` → `useAsyncData` (AGENTS.md rule)
- index.vue: добавлена вкладка "Мои" через `useAyanMy`
- useAyanTrips: `fetchTrip(id)` mock теперь ищет по ID в useState store, не генерирует рандом
- useAyanRequests: добавлен `fetchRequest(id)` — 详情 страница больше не грузит все запросы
- useAyanTrips: `updateTrip` mock сохраняет данные существующей поездки
- trip/[id].vue, request/[id].vue: `onMounted` → `useAsyncData`
- trip/[id].vue, request/[id].vue: hardcoded "Отклики" → `t('ayan.responses')`
- Types: `AyanTripCreate.comment`, `AyanRequestCreate.description`/`time` → `string` (не `null`)
- i18n: `ayan.respond.messagePlaceholder` → нейтральное "Напишите сообщение..." (не "водителю")
- useAPI.ts: добавлен `patch` метод, убраны старые AYAN orders mock handlers
- mockData.ts: удалён мёртвый код (AyaniOrder, generateMockOrders, дублирующиеся константы)

### Design decisions
- Подход C: AYAN composables в services/ayan, используют корневой useAPI для HTTP
- Типы строго по API контракту (trips/requests/responses, не orders)
- Nuxt UI: UForm+UFormField+UInput+UInputNumber+UTextarea+UCard+UTabs+UButton
- Mock store: useState для стабильных ID при детальном просмотре

### Verified
- typecheck ✅ lint ✅

---

## 2026-04-19 14:00 — Vault Audit & Restructure

### Проблема
3 дублирующих AI конфига (vault/CLAUDE.md, vault/AGENTS.md, vault/.opencode/agents.md). Нет инвентаря кода. WikiLinks — шум для ИИ. Церемониальный workflow.

### Изменения
- **Удалены** vault/CLAUDE.md, vault/AGENTS.md, vault/.opencode/agents.md
- **Создан** vault/CODE_MAP.md — полный инвентарь кода (composables, components, pages, types, utils, config, plugins, middleware, layouts, service layers, backend, API status)
- **Обновлён** root AGENTS.md — единый конфиг, упрощённый workflow (sprint → CODE_MAP → wiki → код), без церемоний
- **Обновлён** vault/master_index.md — WikiLinks → обычные пути, добавлен CODE_MAP
- **Обновлён** vault/sprint.md — WikiLinks убраны, статусы: TODO/IN_PROGRESS/DONE/BLOCKED
- **Создан** vault/logs/changelog-archive.md — старые записи перенесены

### Результат
Один AGENTS.md = все правила. CODE_MAP.md = где что в коде. ИИ читает ~50 строк конфига вместо 3 файлов.

---

## 2026-04-19 — Vault Cleanup & Sprint Setup

### Deleted (from /raw — Phase 0 отработан)
- `vault/raw/foundation-audit.md`, `foundation-spec.md`, `foundation-phase-0-spec.md`, `SPEC.md`, `ayan-api-contract.md`

### Moved (raw → wiki)
- `raw/SPEC.md` → `wiki/architecture/roadmap.md`
- `raw/ayan-api-contract.md` → `wiki/services/ayan/api-contract.md`

### Created
- `vault/sprint.md` — Phase 1 AYAN MVP, 9 задач
- `vault/wiki/services/ayan/` — директория

---

## 2026-04-19 — Foundation Phase 0 Complete ✅

### Added
- useAuth.ts — TMA initData + OAuth, unified login
- auth.ts middleware — route protection
- init.ts plugin — Telegram SDK + auto-login
- auth/callback.vue — OAuth callback
- useGlobalError.ts — global error state
- error-handler.ts — global handler
- validators.ts — 8 validators
- forms.ts — form types
- useStorage.ts — localStorage wrapper
- useNetwork.ts — online/offline
- ui.ts — UI types
- sah.json — Yakut language

### Verified
- typecheck ✅ lint ✅

---

## 2026-04-19 — Foundation Phase 0 Spec

- vault/raw/foundation-phase-0-spec.md — спецификация Phase 0
- vault/wiki/architecture/auth-flow.md — дизайн авторизации
- 10 критичных проблем найдено, план реализации (0.7–0.10)
