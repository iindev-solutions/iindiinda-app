# Session Ledger

## 2026-04-23 10:55 — Vault English Standard

- Scope: establish vault as mandatory memory and switch active vault workflow docs to English
- Changes: rewrote `AGENTS.md`, `vault/master_index.md`, added `vault/WORKFLOW.md`, added `vault/SESSION_LEDGER.md`, rewrote active handoff docs in English
- Verified: documentation structure reviewed after edit
- Blockers: older historical vault pages outside the active entry set are still mixed-language and will need incremental rewrite
- Next: keep all future vault updates in English and continue translating active operational docs when touched

## 2026-04-23 11:20 — Empty Template Scaffold

- Scope: create a new minimal starter project under `empty-template/`
- Changes: added starter frontend, starter backend, and a complete vault-first knowledge base inside `empty-template/`
- Verified: key starter files and vault entry docs were read back after creation
- Blockers: `empty-template/backend/` is a template scaffold, not a full generated Laravel runtime
- Next: adapt the template only when a new real project direction is defined in its own vault

## 2026-04-23 15:52 — VPS Stop-Point Audit

- Scope: recover the exact AYAN stop point and verify whether the old SSH blocker still exists
- Changes: audited mandatory vault files, checked local repo/frontend verification, probed VPS SSH/runtime, and updated handoff docs to reflect the new blocker
- Verified: `npm run typecheck`, `npm run lint`, `npm run test`, VPS `phpunit` (`6 tests, 69 assertions`), `php artisan route:list --path=api/ayan`, `curl -I http://89.22.226.34/api/health`
- Blockers: VPS checkout is dirty and 5 commits behind `origin/front/ayan`, so latest committed hardening is not yet deployed cleanly
- Next: preserve or reconcile dirty VPS edits, sync VPS to branch tip, then rerun backend verification and AYAN smoke

## 2026-04-23 16:12 — VPS Sync + Role Logic Audit

- Scope: preserve old VPS backend state, sync live backend to branch tip, verify backend hardening, and audit current driver/passenger logic
- Changes: backed up dirty VPS state, stashed/synced `/var/www/iind-app/backend` to `1fd837f`, ran backend verification on clean deployed state, ran GitHub Pages build, and documented the missing frontend role-switch UI gap
- Verified: `npx nuxt build --preset github_pages`, VPS `phpunit` (`13 tests, 94 assertions`), `php artisan route:list --path=api/ayan`, `curl -I http://89.22.226.34/api/health`
- Blockers: current app flow still lacks frontend role-switch UI even though backend/composable support exists
- Next: manually test live role flow and decide whether to implement role switching in frontend

## 2026-04-23 17:15 — Past Item UX Slice

- Scope: hide expired items from public feed, keep them visible in My/details, and render zero price as free
- Changes: updated AYAN frontend formatter/detail/list logic, added localized free/past labels, added backend upcoming-feed and expired-response guards in local code, and documented VPS static frontend hosting as the likely preferred path over GitHub Pages
- Verified: `npm run test`, `npm run lint`, `npm run typecheck`, `npx nuxt build --preset github_pages`, isolated VPS `php -l` on changed backend files, isolated VPS `php artisan route:list --path=api/ayan`
- Blockers: full backend feature-test execution for the modified local slice was not reproducible in isolated VPS copy because copied MySQL runs collided with server test tables and SQLite driver is unavailable on VPS PHP
- Next: decide whether to commit/deploy this local slice, then continue live AYAN verification and role-switch UX work

## 2026-04-23 17:37 — Role Switch + VPS SPA HTTP

- Scope: expose driver/passenger switching in AYAN UI and start moving frontend hosting from GitHub Pages to the VPS
- Changes: added AYAN role switcher component and role utility, built frontend static bundle, uploaded it to VPS, and switched Nginx default site to serve the SPA at `/` while preserving backend API under `/api`
- Verified: `npm run test`, `npm run lint`, `npm run typecheck`, `npx nuxt build --preset github_pages`, `curl -I http://89.22.226.34/`, `curl -I http://89.22.226.34/ayan`, `curl -I http://89.22.226.34/api/health`, `nginx -t`
- Blockers: VPS frontend still has no trusted HTTPS because there is no hostname/domain yet
- Next: manually verify live role switching in browser/TMA and attach a hostname so TLS can be issued

## 2026-04-23 17:56 — Push + Backend Deploy Green

- Scope: push the new AYAN slice, deploy backend changes to VPS, and verify the real checkout end-to-end at the command level
- Changes: pushed `front/ayan`, pulled VPS repo forward, corrected one backend test expectation for `my/*` history, and re-ran the focused backend suite on the live checkout
- Verified: `git push origin front/ayan`, VPS `git pull --ff-only origin front/ayan`, VPS backend `phpunit` (`15 tests, 112 assertions`), `curl -I http://89.22.226.34/`, `curl -I http://89.22.226.34/ayan`, `curl -I http://89.22.226.34/api/health`
- Blockers: HTTPS still needs a hostname/domain before TLS can be issued
- Next: set up DuckDNS or a real domain, issue TLS, then manually verify the live role-switch/past-item flow in browser/TMA

## 2026-04-23 19:31 — DuckDNS HTTPS Enabled

- Scope: bind a real hostname to the VPS and enable trusted HTTPS for the SPA + API deployment
- Changes: updated DuckDNS to point at the VPS, set Nginx `server_name` to `iindiinda.duckdns.org`, issued a Let's Encrypt certificate through Certbot, enabled HTTP-to-HTTPS redirect, and installed a DuckDNS updater cron job on VPS
- Verified: `nslookup iindiinda.duckdns.org`, `curl -I http://iindiinda.duckdns.org/`, `curl -I https://iindiinda.duckdns.org/`, `curl -I https://iindiinda.duckdns.org/api/health`, direct execution of `/opt/duckdns/update.sh`
- Blockers: manual live browser/TMA verification is still pending
- Next: test the secure deployment end-to-end from browser/Telegram and record the result

## 2026-04-23 23:53 — Frontend Auth Gate Cleanup

- Scope: close the guest-browser auth gap and remove production `devInitData` fallback from the deployed SPA
- Changes: added AYAN access-state/auth helpers, added Telegram-only and auth-failed UI states, removed `NUXT_PUBLIC_DEV_INIT_DATA=test` from `frontend/.env`, rebuilt the SPA, and redeployed the frontend bundle with safe production config and stable HTTPS SPA fallback
- Verified: `npm run test`, `npm run lint`, `npm run typecheck`, `npx nuxt build --preset github_pages`, deployed HTML contains `apiBase:"/api"` and `devInitData:""`, `curl -I https://iindiinda.duckdns.org/`, `curl -I https://iindiinda.duckdns.org/ayan`, unauthenticated `/api/user` returns `401`
- Blockers: real Telegram/TMA verification is still pending
- Next: manually test login and role switching in the Telegram Mini App, then decide whether to commit the local auth-gate slice

## 2026-04-24 12:05 — VPS Telegram Token Fix

- Scope: find the real cause of the TMA login failure on the live HTTPS deployment
- Changes: verified the frontend no longer ships prod `test` fallback, found missing `TELEGRAM_BOT_TOKEN` on VPS, added the provided token directly to VPS `.env`, and cleared Laravel caches
- Verified: VPS env now contains the token, fake `/api/auth/telegram` requests now fail as `Telegram user data is invalid.` instead of `Telegram auth is not configured.`, `/api/health` remains green
- Blockers: manual retest from the real Telegram Mini App is still pending
- Next: reopen the Mini App from the bot and verify login/role switching now works end-to-end

## 2026-04-24 12:36 — AYAN Entry Polish Live

- Scope: improve detail-page back navigation and polish the create form UX for price/date entry
- Changes: added helper-tested back-button display logic, forced visible back button on AYAN detail pages, replaced the price stepper with a normal text price field, switched date picking to Nuxt UI calendar, pushed commit `87a4815`, synced VPS repo, and deployed the rebuilt SPA bundle to HTTPS
- Verified: `npm run test` (`13 tests`), `npm run lint`, `npm run typecheck`, `npx nuxt build --preset github_pages`, VPS repo `87a4815`, `curl -I https://iindiinda.duckdns.org/ayan`, `curl -I https://iindiinda.duckdns.org/api/health`
- Blockers: full manual Telegram/browser verification is still pending
- Next: manually verify back navigation, price field UX, calendar restrictions, and the full AYAN flow inside TMA
