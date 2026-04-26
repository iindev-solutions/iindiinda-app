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

## 2026-04-24 11:56 — Response Status + Zoom Fix Live

- Scope: stop duplicate response UX confusion and remove iPhone auto-zoom from the AYAN create slideover
- Changes: used `/ayan/my/responses` to detect the current user's existing response on detail pages, replaced repeat response form with a status card, added response cards to `My`, applied `fixed` sizing to slideover form controls, pushed `09c654b`, synced VPS repo, and deployed the rebuilt SPA bundle
- Verified: `npm run test` (`15 tests`), `npm run lint`, `npm run typecheck`, `npx nuxt build --preset github_pages`, VPS repo `09c654b`, `curl -I https://iindiinda.duckdns.org/ayan`, `curl -I https://iindiinda.duckdns.org/api/health`
- Blockers: manual TMA/iPhone verification is still pending
- Next: confirm duplicate-response status UX and slideover no-zoom behavior on real devices

## 2026-04-24 18:39 — Vault Refresh For Clean Restart

- Scope: prepare accurate vault handoff before starting a new session
- Changes: re-read mandatory vault files, rewrote `vault/sprint.md` and `vault/resume-plan.md`, and documented exact legal-pack vs lifecycle-slice split
- Verified: `git status --short --branch`, `git log --oneline --decorate -12`, and updated vault files now point to one clear next action
- Blockers: legal and lifecycle work still coexist in same local diff, including overlap in `frontend/services/ayan/app/pages/ayan/index.vue` and locale files
- Next: run legal-only staging, commit/push/deploy legal routes, then keep lifecycle slice for separate finish and verification

## 2026-04-24 19:00 — Legal Pack Ship Complete

- Scope: isolate, ship, and verify AYAN legal pages/links without mixing unfinished lifecycle status work
- Changes: staged legal-only hunks/files, committed `f13f6b6`, pushed `front/ayan`, synced VPS repo, rebuilt frontend static bundle, deployed to `/var/www/iind-app/frontend/public`, and reloaded Nginx
- Verified: `npm run test`, `npm run lint`, `npm run typecheck`, `npx nuxt build --preset github_pages`, live URL checks for `/`, `/ayan`, `/legal/ayan-terms`, `/legal/privacy`, `/legal/ayan-safety`, and `/api/health` (all `200`)
- Blockers: lifecycle expansion backend/frontend slice is still local and in progress
- Next: finish lifecycle behavior, run isolated verification for that slice, then ship separately

## 2026-04-24 19:50 — Lifecycle Slice Ship Complete

- Scope: ship AYAN lifecycle statuses (`matched/completed/cancelled`) as a separate safe deployment slice
- Changes: committed and pushed `a3591a0`, migrated VPS backend, deployed rebuilt frontend bundle, and synced runtime to new lifecycle behavior in detail/my-response surfaces
- Verified: `npm run test`, `npm run lint`, `npm run typecheck`, `npx nuxt build --preset github_pages`, VPS backend phpunit (`16 tests, 127 assertions`), live URL checks for `/`, `/ayan`, `/legal/ayan-terms`, and `/api/health` (all `200`)
- Blockers: manual real-device Telegram/browser E2E validation is still pending
- Next: run full manual lifecycle flow verification and capture any production edge-case fixes

## 2026-04-24 20:00 — Lifecycle API Smoke Validation

- Scope: validate shipped lifecycle behavior on live API before manual Telegram device pass
- Changes: ran synthetic production API flow with isolated smoke users/tokens for trip/request lifecycle transitions and response deletion guards, then cleaned smoke users/tokens from production DB
- Verified: trip and request both pass `open -> matched -> completed/cancelled`, `my/responses` includes linked target statuses, non-pending response delete returns `422`, smoke users removed (`COUNT=0`), live `/`, `/ayan`, and `/api/health` remain `200`
- Blockers: Telegram Mini App UI interaction still needs human device verification
- Next: perform manual TMA/browser UI pass and capture outcome in vault

## 2026-04-24 20:10 — Final Sync For Next-Day Resume

- Scope: ensure local, remote, and VPS repositories are synchronized before pause
- Changes: verified branch alignment, pulled latest vault commits on VPS, and refreshed resume/sprint stop point timestamps
- Verified: local HEAD = `219387d`, origin HEAD = `219387d`, VPS HEAD = `219387d`, `git status` clean, live `/api/health` still `200`
- Blockers: only manual Telegram/browser UI E2E remains
- Next: continue from `vault/resume-plan.md` with manual lifecycle UI verification

## 2026-04-24 20:15 — Final Hash Lock For Tomorrow

- Scope: lock final end-of-day commit hash after sync-note commit
- Changes: pushed `docs(vault): record final sync checkpoint` and pulled it on VPS
- Verified: local/origin/VPS all at `d019d0c`, working tree clean
- Blockers: only manual Telegram/browser UI E2E remains
- Next: tomorrow continue from `d019d0c` using `vault/resume-plan.md`

## 2026-04-25 09:25 — Production Asset MIME Hotfix

- Scope: investigate and recover live AYAN startup failures caused by blocked module/CSS loads
- Changes: traced MIME errors to missing hashed files in `/var/www/iind-app/frontend/public/assets`, compared local `.output/public/assets` against VPS, and uploaded the missing files directly to VPS
- Verified: affected files now return `200` with correct MIME types (`application/javascript` / `text/css`), and local-vs-VPS asset diff shows no missing files
- Blockers: Nginx SPA fallback still rewrites unknown `/assets/*` to `/index.html`, which can hide future missing asset deploy drift
- Next: harden Nginx config for `/assets/` (`try_files $uri =404`) and add deploy-time asset integrity check before manual Telegram/browser E2E

## 2026-04-25 09:42 — Full SPA Rebuild + Full Redeploy

- Scope: replace partial asset recovery with a full SPA rebuild and full-bundle VPS redeploy
- Changes: rebuilt frontend (`npx nuxt build --preset github_pages`), uploaded complete `.output/public` archive, deployed via directory swap (`public_new` -> `public`), and copied prior hashed assets from `public_prev/assets` for cache compatibility
- Verified: local-vs-VPS full public parity check is `MATCH`, rebuilt asset set exists on VPS (`LOCAL_SET_PRESENT`), previously failing asset URLs now return correct MIME, and `/ayan` stays `200`
- Blockers: Nginx still masks missing assets with SPA HTML fallback (`/assets/*` not forced to `404` yet)
- Next: continue manual Telegram/browser lifecycle E2E, then apply Nginx asset-fallback hardening in a focused ops slice

## 2026-04-25 09:55 — Telegram Auth Runtime Diagnostic

- Scope: verify whether live Telegram bot auth failure is caused by missing/invalid bot token
- Changes: checked VPS backend env token presence, validated token against Telegram `getMe`, checked bot `getChatMenuButton` web_app URL, and cleared Laravel config/app caches
- Verified: token exists and is valid for `@iind_app_bot`, web_app URL remains `https://iindiinda.duckdns.org/`, cache clear commands succeeded, and auth endpoint follows Telegram validation path instead of missing-config path
- Blockers: real Telegram-session failure still requires a fresh user attempt timestamp for exact request correlation
- Next: capture one real-device retry timestamp and inspect matching `/api/auth/telegram` log line/status to isolate remaining auth failure path

## 2026-04-25 10:15 — Telegram Auth Payload Format Fix

- Scope: fix cross-account Telegram login failure after confirming bot token/config are valid
- Changes: identified production mismatch where JSON `init_data` path failed validation, switched frontend `loginWithInitData` call to form-urlencoded payload, rebuilt SPA, and redeployed full bundle on VPS
- Verified: auth unit tests pass, typecheck passes, production entry bundle contains the new content-type path, and live endpoint receives form-urlencoded payload through Telegram validation branch
- Blockers: no fresh post-fix real Telegram retry captured yet in this session
- Next: run immediate bot login retry from Telegram and confirm `/api/auth/telegram` returns `200` for real signed initData

## 2026-04-25 10:50 — Telegram Bootstrap Audit

- Scope: audit AYAN TMA startup after report that Telegram `initData` was unavailable and AYAN could not open
- Changes: traced frontend bootstrap race in `useTg`/init plugin, added Telegram wait helpers, made Telegram state reactive, added delayed auto-login retry, and preserved mock-auth behavior alongside real form-urlencoded login
- Verified: `frontend npm run test` (`21/21`), `frontend npm run typecheck`, `frontend npm run build`, and final scoped review found no issues in changed Telegram/auth files
- Blockers: real Telegram Mini App validation still requires deploy plus manual device retry
- Next: deploy current frontend Telegram bootstrap fix, then retest `/ayan` from real Telegram and record the exact runtime outcome

## 2026-04-25 10:59 — Sync Local GitHub VPS

- Scope: commit the Telegram bootstrap fix and align repository state across local, GitHub, and VPS
- Changes: created commit `110c550`, pushed `front/ayan` to origin, and fast-forwarded `/var/www/iind-app` on VPS to the same commit
- Verified: local/origin/VPS all resolve to `110c550`; local worktree is aligned with origin; VPS repo is aligned with origin and only keeps untracked deploy directories
- Blockers: live frontend bundle still needs redeploy before real Telegram Mini App validation can confirm behavior
- Next: redeploy frontend from synced commit `110c550`, then retry `/ayan` inside Telegram and capture exact outcome

## 2026-04-25 11:12 — Live Frontend Redeploy

- Scope: publish the synced Telegram bootstrap fix to the live frontend bundle
- Changes: rebuilt frontend with `npx nuxt build --preset github_pages`, uploaded `.output/public` to VPS, deployed via `public_new -> public` swap, and copied prior hashed assets forward for cache compatibility
- Verified: live `/`, `/ayan`, `/api/health`, current JS asset `BfQflojk.js`, and current CSS asset `entry.CiIJ0BEA.css` all return `200`; live root HTML references current asset hashes; VPS repo remains at `9cc064d`
- Blockers: real Telegram Mini App validation still requires manual device retry
- Next: open `/ayan` inside Telegram Mini App and confirm signed `initData` login now succeeds; if it fails, capture one fresh retry timestamp for `/api/auth/telegram` correlation

## 2026-04-25 12:42 — TMA Root Cause and Prevention

- Scope: finish the AYAN TMA auth audit, isolate the exact regression cause, and add prevention so future static redeploys cannot silently poison production API base
- Changes: confirmed legal-pack commit was unrelated to auth logic, traced the real regression to live static HTML baking local `frontend/.env` API base plus fragile Telegram bootstrap timing, extended Telegram wait defaults, added runtime `/api` normalization on HTTPS, added static HTML guard utilities/tests, and added guarded `npm run build:static` deploy command plus repo instruction update
- Verified: `frontend npm run test` (`28/28`), `frontend npm run typecheck`, `frontend npm run build:static` (`STATIC_API_BASE_OK`), raw `npx nuxt build --preset github_pages` + `node scripts/verify-static-api-base.mjs` (`STATIC_API_BASE_OK`), built HTML contains `apiBase:"/api"`, live HTML contains `apiBase:"/api"`, and final focused review found no issues
- Blockers: still waiting for one clean Telegram Mini App retry after the corrected `/api` live bundle to verify the first real `/api/auth/telegram` request path
- Next: retry AYAN once from Telegram Mini App, then inspect nginx access log for the first `/api/auth/telegram` hit or failure status

## 2026-04-25 14:30 — RF Legal Audit + Platform Legal Center

- Scope: audit all service visions for RF legal posture and expand shared legal surfaces beyond the old AYAN-only slice
- Changes: added platform legal center routes, user agreement/privacy/consent/support pages, UUS/TAL/AGAL rules pages, shared footer legal bar, expanded legal-link helpers, Russian legal copy in `frontend/i18n/locales/ru.json`, and safer UUS/TAL/AGAL placeholder positioning; documented findings in `vault/wiki/architecture/legal-rf-audit.md`
- Verified: `frontend JSON.parse(frontend/i18n/locales/ru.json)`, `frontend npm run test`, targeted eslint on changed legal files, `frontend npm run typecheck`, `frontend npm run build:static`
- Blockers: source changes are not deployed yet; final operator/requisites details are still missing; RF personal-data localization remains unresolved because current runtime history points to Sweden-hosted infrastructure; full repo lint still has unrelated pre-existing CRLF/prettier debt outside this slice
- Next: decide RF localization/hosting plan, fill final operator details, then deploy the new legal center when approved

## 2026-04-25 15:05 — Legal Rendering Fix + Navigation Simplify

- Scope: fix broken legal text rendering and reduce repeated legal-entry surfaces to one place in the main menu
- Changes: resolved vue-i18n `tm()` rendering bug by using `rt()` in `frontend/app/components/LegalDocumentPage.vue`, removed repeated legal links/blocks from AYAN/UUS/TAL/AGAL screens and default layout, and kept the legal-center entry only on the home bottom card
- Verified: targeted eslint on changed files, `frontend npm run typecheck`, `frontend npm run build:static`
- Blockers: fix is local until committed/pushed/deployed; legal content itself still needs final operator details and RF hosting closure
- Next: commit this fix and redeploy live if approved

## 2026-04-25 14:45 — Legal Center Commit Push Deploy

- Scope: finalize the current legal-center slice by committing, pushing, and redeploying it to live VPS hosting
- Changes: committed `287b95c`, pushed `front/ayan`, fast-forwarded VPS repo, uploaded rebuilt static bundle, swapped `public_new -> public`, and preserved older hashed assets for cache compatibility
- Verified: origin and VPS head at `287b95c`, live HTML references current assets `GRAbHFO1.js` + `entry.CaE_wa2P.css`, and live `/`, `/legal`, `/legal/uus-rules`, `/legal/tal-rules`, `/legal/agal-rules`, `/api/health` all return `200`
- Blockers: legal closure is still not final because operator details, public requisites, RF personal-data localization, and counsel review remain pending
- Next: continue legal work from document finalization, not from UI plumbing

## 2026-04-25 15:20 — Legal Render Fix Deploy

- Scope: publish the legal rendering fix and simplified legal navigation to live VPS frontend hosting
- Changes: committed `f5a6f21`, pushed `front/ayan`, fast-forwarded VPS repo, rebuilt static bundle, redeployed `frontend/public` via `public_new -> public`, and preserved older hashed assets for cache compatibility
- Verified: VPS head at `f5a6f21`, live `/`, `/legal`, `/legal/ayan-terms`, and `/api/health` return `200`; local `frontend npm run build:static` stayed green
- Blockers: legal content still needs final operator details, RF hosting/localization closure, and counsel review
- Next: continue legal work from content finalization and operator details, not from rendering/UI bugfixes

## 2026-04-25 15:45 — Collapsible Service Explainers

- Scope: replace always-visible service explanation blocks with collapsed-by-default descriptions and concrete examples on each service entry screen
- Changes: added shared `AppServiceAbout` component, wired it into AYAN/UUS/TAL/AGAL pages, added Russian description/example copy in `frontend/i18n/locales/ru.json`, committed `728a5ee`, pushed `front/ayan`, and redeployed the static frontend bundle to VPS
- Verified: locale JSON parse, targeted eslint on changed files, `frontend npm run typecheck`, `frontend npm run build:static`, VPS head at `728a5ee`, and live `200` checks for `/`, `/ayan`, `/uus`, `/tal`, `/agal`
- Blockers: no new blocker from this UI slice; broader legal finalization still needs operator details, RF hosting/localization closure, and counsel review
- Next: continue legal-content finalization or adjust service copy/examples if product wording changes

## 2026-04-26 00:10 — Legal Text Gap Review

- Scope: review all current Russian legal texts and identify what still must be added before production-grade legal sign-off
- Changes: audited legal center copy, platform docs, AYAN safety/rules, and UUS/TAL/AGAL rules against the RF legal audit notes
- Verified: reviewed `frontend/i18n/locales/ru.json`, `vault/wiki/architecture/legal-rf-audit.md`, and `vault/CODE_MAP.md`
- Blockers: operator/controller details, hosting/localization posture, retention/processor disclosure, and final counsel review are still unresolved
- Next: collect real operator data and hosting facts, then patch each legal text with the missing clauses

## 2026-04-26 00:40 — TMA No-Zoom Fix + AYAN Example Copy

- Scope: quickly reduce Telegram/iOS zoom breakage in the AYAN create flow and make the AYAN explainer examples more concrete
- Changes: added zoom-safe CSS/classes for AYAN create-form inputs and calendar buttons, updated `AyanCreateSlideover.vue`, and refreshed AYAN service-about copy in `frontend/i18n/locales/ru.json`
- Verified: `JSON.parse(frontend/i18n/locales/ru.json)`, `frontend npm run typecheck`, `frontend npm run build:static`
- Blockers: real Telegram device verification is still needed to confirm the zoom issue is fully gone in the affected TMA flow
- Next: retest create trip/request inside Telegram Mini App and confirm focus/calendar open no longer trigger disruptive zoom

## 2026-04-26 04:30 — TMA No-Zoom Fix Commit + Live Deploy

- Scope: ship the AYAN TMA no-zoom fix to production so it can be tested inside the real Telegram Mini App
- Changes: committed runtime fix `52da837`, pushed `front/ayan`, fast-forwarded VPS repo, redeployed `frontend/.output/public` to live static hosting with directory swap + old-asset cache compatibility, then synced vault notes in `d9ef630`
- Verified: deployed runtime code at `52da837`, local/origin/VPS repo aligned on the latest post-deploy vault-sync tip, `frontend npm run typecheck`, `frontend npm run build:static`, live `/`, `/ayan`, `/api/health`, and deployed asset URLs all return `200`
- Blockers: only real-device Telegram verification remains for this zoom-specific slice
- Next: open AYAN in Telegram Mini App and check input focus + calendar open behavior on the create form

## 2026-04-26 05:40 — TMA Create Form Simplify + Live Deploy

- Scope: reduce the remaining Telegram Mini App zoom/focus weirdness by simplifying the AYAN create slideover instead of only forcing larger font sizes
- Changes: replaced the popover calendar with a native date input, disabled slideover transition in Telegram, made trip comment required in frontend validation/payload, updated ride placeholders in RU/SAH, committed `5e81817`, pushed `front/ayan`, and redeployed the live static bundle
- Verified: locale JSON parse for `ru.json` and `sah.json`, `frontend npm run typecheck`, `frontend npm run build:static`, live `/ayan`, `/api/health`, and current deployed asset URLs return `200`
- Blockers: no blocker reported for this zoom-specific slice after the real Telegram retest; broader AYAN E2E still remains
- Next: continue AYAN Telegram Mini App E2E beyond create-form zoom, especially create/respond/accept/matched/completed/cancelled flows

## 2026-04-26 06:00 — AYAN MVP Manual TMA Verification Green

- Scope: capture the completed real-device Telegram Mini App verification outcome for AYAN MVP
- Changes: recorded the user's final manual QA result that the zoom issue is gone and the AYAN MVP flow is working well enough for MVP acceptance
- Verified: user-reported real-device testing is green; no remaining AYAN blocker was reported for MVP scope
- Blockers: no active AYAN MVP runtime blocker remains from this manual pass
- Next: decide whether to switch to legal finalization or the next product/service phase

## 2026-04-26 06:20 — AGAL Chosen As Next Track

- Scope: lock the first post-AYAN implementation target so next sessions do not need to re-decide direction
- Changes: recorded that AGAL goes next while legal waits, added `vault/wiki/services/agal/api-contract.md`, and updated sprint/resume docs to treat AGAL as the active next build target
- Verified: reviewed AGAL vision, roadmap, system design, current placeholder AGAL frontend/backend state, and the new AGAL contract doc
- Blockers: AGAL still has only placeholder code in app/backend and now needs actual implementation slices
- Next: start AGAL by aligning backend route shape and scaffolding frontend structure to reuse AYAN patterns

## 2026-04-26 07:10 — AGAL Scaffold Slice

- Scope: land the first AGAL code slice by aligning route shape and building the frontend scaffold around the new contract
- Changes: replaced backend `/agal/parcels*` stubs with contract-shaped scaffold endpoints, added AGAL frontend types/composables/page/component structure, rewrote the AGAL layer README, updated AGAL locale copy, committed `274e615`, pushed `front/ayan`, synced VPS repo, and redeployed the live frontend bundle
- Verified: locale JSON parse, `frontend npm run typecheck`, `frontend npm run build:static`, remote `php -l` against the changed backend routes file, live `/agal` `200`, live `/api/health` `200`, and guest `/api/agal/routes` `401` as expected
- Blockers: AGAL still has placeholder backend behavior only; no real persistence/controllers/details yet
- Next: replace AGAL placeholder closures with real controllers/models/migrations and then wire create/feed UI to real data
