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

## 2026-04-26 17:20 — DESIGN.md Baseline

- Scope: start the redesign track by defining a shared design-system source of truth
- Changes: added root `DESIGN.md` with alpha tokens for colors, typography, spacing, rounding, and shared component patterns; updated vault handoff docs to point next work at shell/primitives implementation
- Verified: `node .tmp-designmd/package/dist/index.js lint DESIGN.md` (`0 errors, 0 warnings`)
- Blockers: redesign is still documentation-first; shared frontend components and pages have not been restyled yet
- Next: implement shared shell, cards, buttons, inputs, tabs, and status badges from `DESIGN.md`

## 2026-04-26 18:25 — Shared Redesign Slice

- Scope: implement the first runtime redesign pass on top of the new `DESIGN.md` baseline
- Changes: added shared `AppHero`, rebuilt global shell/theme/nav/cards/about/empty primitives, redesigned home + UUS/TAL landing screens, and restyled AYAN/AGAL entry-feed screens plus access/role cards
- Verified: `cd frontend && npm run typecheck`; `cd frontend && npm run build:static` (`STATIC_API_BASE_OK`)
- Blockers: AYAN/AGAL detail pages and create flows still keep the older visual treatment
- Next: redesign detail pages and create surfaces using the same shared primitives

## 2026-04-26 18:55 — Variant 1 Checkpoint + Nav Fix

- Scope: fix first-tap bottom-nav active state and lock the current redesign as variant 1 before trying a simpler direction
- Changes: updated `frontend/app/components/AppBottomNav.vue` to use `NuxtLink` with optimistic pending-route highlighting; kept current redesign as the first comparison checkpoint
- Verified: `cd frontend && npm run typecheck`; `cd frontend && npm run build:static` (`STATIC_API_BASE_OK`)
- Blockers: redesign can still be simplified further; detail/create screens remain the next large surface
- Next: commit this state as redesign variant 1, then iterate toward a simpler variant 2

## 2026-04-26 19:20 — Simpler Variant 2

- Scope: try a calmer/simpler redesign direction while preserving the saved variant 1 checkpoint
- Changes: reduced ambient effects and gradients, flattened shell/cards/nav styling, simplified the home/service presentation, kept the first-tap nav fix, and restored the AYAN access-state gate
- Verified: `cd frontend && npm run typecheck`; `cd frontend && npm run build:static` (`STATIC_API_BASE_OK`)
- Blockers: variant 2 is still local-only and needs visual comparison/decision before commit or deploy
- Next: compare variant 2 against committed variant 1, then either keep refining or commit the chosen direction

## 2026-04-26 20:05 — Variant 2 Commit + Variant 3 Start

- Scope: checkpoint variant 2, then push a third redesign pass toward simpler daily-use UX
- Changes: committed variant 2 as `b22f92c`, then simplified shared shell/nav/about/empty/role-switch styling again and locked `iind` to cyan brand color in the new local variant 3 work
- Verified: `cd frontend && npm run typecheck`; `cd frontend && npm run build:static` (`STATIC_API_BASE_OK`)
- Blockers: variant 3 is still local-only and needs visual review before commit or deploy
- Next: compare variant 3 against committed variants 1 and 2, then commit the winner before moving into detail/create redesign

## 2026-04-26 21:00 — Variant 3 Full Surface Pass

- Scope: continue variant 3 without stopping at shell/feed screens and carry it into the main working surfaces
- Changes: removed the home `iindiinda` reminder, kept `iind` cyan, added shared detail/form styling, redesigned AYAN/AGAL detail pages, and redesigned both AYAN/AGAL create slideovers
- Verified: `cd frontend && npm run typecheck`; `cd frontend && npm run build:static` (`STATIC_API_BASE_OK`)
- Blockers: redesign is still local-only and not deployed; final visual preference between committed checkpoints and current variant 3 still depends on human review
- Next: keep variant 3, polish any remaining rough edges, then commit/deploy the chosen redesign direction

## 2026-04-26 21:45 — Push Sync Deploy Variant 3

- Scope: push all redesign work, sync repository states, and promote variant 3 to the live frontend runtime
- Changes: pushed `front/ayan`, fast-forwarded VPS repo to `30b0f40`, redeployed `frontend/.output/public` via safe directory swap, and aligned local/GitHub/VPS/live frontend on the same redesign tip
- Verified: `git push origin front/ayan`; `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` (`30b0f40`); `curl -I` for `/`, `/ayan`, `/agal`, `/api/health`; live root HTML still contains `apiBase:"/api"`
- Blockers: post-redesign Telegram/browser regression validation is still needed on real usage flows
- Next: run manual AYAN + AGAL regression checks on the live redesigned build and patch only what fails

## 2026-04-26 21:55 — Manual Redesign Validation Green

- Scope: capture the user's manual live-check result after the variant 3 deployment
- Changes: recorded that the redesigned runtime was checked manually and that everything appears fine in current use
- Verified: user-reported manual validation on the live AYAN + AGAL redesigned build
- Blockers: no active runtime UX blocker reported in this check
- Next: freeze redesign, focus on production hardening / observation / next roadmap choice instead of more speculative UI churn

## 2026-04-26 22:20 — Coolify Starter Layout

- Scope: start the repository-side Coolify migration path by adding deployable container files instead of only discussing architecture
- Changes: added `docker-compose.coolify.yml`, `frontend/Dockerfile.coolify`, `backend/Dockerfile.coolify`, `backend/docker/entrypoint.coolify.sh`, `ops/coolify/*.conf`, `.env.coolify.example`, `.dockerignore`, and starter docs in `ops/coolify/README.md`
- Verified: parsed `docker-compose.coolify.yml` with Node YAML tooling available through local frontend dependencies; `sh -n backend/docker/entrypoint.coolify.sh`
- Blockers: no local `docker` runtime exists in this environment, so the new Coolify stack is not build-tested or deploy-tested yet
- Next: either trial this stack in Coolify or refine it after a first real deploy error/output pass

## 2026-04-26 22:35 — Coolify Exact Setup Doc

- Scope: turn the new Coolify starter files into an operator-usable deployment procedure
- Changes: added `ops/coolify/SETUP.md`, linked it from `ops/coolify/README.md`, and clarified `.env.coolify.example` for trial-domain and Telegram bot id usage
- Verified: new setup doc exists in source and references the current Coolify stack files
- Blockers: still no local `docker` runtime and no real Coolify deploy feedback yet
- Next: create the Coolify Docker Compose resource on a trial subdomain and use first deploy logs as the next debugging input

## 2026-04-26 23:10 — Coolify Prod VPS Attempt Paused

- Scope: push the new Coolify prep, sync it to VPS, and test whether the current production VPS can host Coolify directly
- Changes: committed/pushed `a333560`, synced VPS repo, ran an in-place Coolify install attempt, inspected installer logs, and reclaimed Docker disk space with `docker system prune -af --volumes`
- Verified: install failure is currently at Docker image pull time for `ghcr.io/coollabsio/coolify:4.0.0-beta.474` with `lookup ghcr.io: i/o timeout`; free disk improved to about `5.2GB` after cleanup but host capacity still remains far below Coolify's recommended headroom
- Blockers: SSH/HTTP became intermittent during install/restart windows; Coolify source files now exist under `/data/coolify/source`, but the control plane is not installed successfully yet
- Next: tomorrow start with VPS health/stability checks, then inspect Docker DNS/network behavior and decide whether to keep trying on this small production VPS or switch to a safer Coolify host plan

## 2026-04-26 23:25 — VPS Recheck Still Bad

- Scope: verify whether the production VPS had recovered enough to continue after the paused Coolify attempt
- Changes: retried SSH and HTTPS checks against the current VPS/live domain
- Verified: SSH is still intermittent and only sometimes accepts a session after retries; live HTTPS root checks still time out in repeated attempts
- Blockers: host health is still degraded, so application/Coolify work would be guesswork right now
- Next: use the provider panel for a clean VPS reboot, then perform only basic service/route health checks before any deeper action

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

## 2026-05-02 10:20 — Fresh VPS Redeploy Planning

- Scope: re-evaluate deployment after operator-reported VPS reinstall and define the new restore path
- Changes: re-read the mandatory vault docs plus deploy-relevant notes, checked local SSH alias/key state, probed the rebuilt host, captured the new SSH fingerprint, and reset the plan from "recover old VPS" to "manual redeploy on fresh VPS"
- Verified: `git status --short --branch`; `git log -1 --oneline --decorate`; local `~/.ssh/config`; `ssh-keyscan`/SSH probe against `89.22.226.34`
- Blockers: local SSH trust is stale because the host key changed after reinstall; key authorization on the rebuilt host is not yet revalidated after the trust reset
- Next: verify the new fingerprint out-of-band, rotate `known_hosts`, retest SSH with the existing key, then bootstrap Nginx/PHP/MySQL + backend/frontend deploy on the fresh host

## 2026-05-02 10:35 — SSH Trust Fixed Key Auth Blocked

- Scope: clear the post-reinstall SSH host-key warning and test whether the old automation key still works
- Changes: verified the new ED25519 host fingerprint, removed stale local `known_hosts` entries, added the rebuilt host back to local trust, and retried `ssh iind-vps`
- Verified: host-key mismatch is resolved; SSH now fails with `Permission denied (publickey,password)`
- Blockers: rebuilt VPS does not currently authorize `C:/Users/slavk/.ssh/iind_vps` for `root`
- Next: add `C:/Users/slavk/.ssh/iind_vps.pub` to `/root/.ssh/authorized_keys` through the working PuTTY/root path, then retry SSH and continue bootstrap

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

## 2026-04-26 08:10 — AGAL Backend Persistence Slice

- Scope: replace AGAL placeholder backend behavior with real persisted routes/requests/responses before moving on to frontend create/feed UX
- Changes: added AGAL migrations, models, controllers, serializer trait, and targeted PHPUnit coverage; replaced AGAL placeholder route closures with real controllers; committed `4fa4f53`, pushed `front/ayan`, synced VPS repo, and ran live VPS migration/test verification
- Verified: remote `php -l` on changed backend files, VPS `php artisan migrate --force`, VPS `./vendor/bin/phpunit tests/Feature/AgalPersistenceTest.php` (`4 tests, 60 assertions`), and VPS `php artisan route:list --path=api/agal`
- Blockers: AGAL frontend still does not expose the real persisted flow yet; current `/agal` page is scaffold-only
- Next: build AGAL create/feed/detail frontend on top of the now-live backend persistence layer

## 2026-04-26 08:45 — AGAL Frontend MVP Slice

- Scope: replace the AGAL scaffold UI with a real frontend MVP flow on top of the shipped backend persistence layer
- Changes: added AGAL role switcher, create slideover, route/request detail pages, AGAL role/response helpers, and rewrote the AGAL index page into a real feed/my-area flow; expanded AGAL locale copy, updated the AGAL README, committed `53af2d7`, pushed `front/ayan`, synced VPS repo, rebuilt static frontend, and redeployed live `/agal`
- Verified: locale JSON parse for `ru.json` and `sah.json`, `frontend npm run typecheck`, `frontend npm run build:static`, VPS head at `53af2d7`, live `/agal` `200`, live `/agal/route/1` `200`, live `/api/health` `200`, guest `/api/agal/routes` `401`, and live root HTML still contains `apiBase:"/api"`
- Blockers: AGAL still lacks real-device Telegram Mini App validation; runtime/device-specific bugs may still surface only after manual testing
- Next: manually verify AGAL in Telegram/browser runtime and patch any create/respond/contact issues found there

## 2026-04-26 09:00 — Redesign Handoff Locked

- Scope: freeze the current live product state and redirect the next session away from new feature work into redesign
- Changes: recorded that AYAN and AGAL are acceptable working baselines for now, legal remains parked, and the next session should begin a frontend-first redesign from shared shell/design-system primitives outward
- Verified: user direction is explicit — redesign now before deeper UI growth; prior live AYAN/AGAL baseline verification already exists in vault
- Blockers: no redesign spec exists yet, so the next session must first define the visual/system direction before touching many pages
- Next: start redesign in this order — shared shell/primitives, then home/service landing pages, then feed/detail/create flows

## 2026-04-29 13:36 — Coolify Resume Attempt Blocked By Host Outage

- Scope: resume the paused production-VPS Coolify recovery carefully, starting with read-only host health checks
- Changes: re-read the mandatory vault/deploy docs, checked current live domain reachability, retried SSH access to `iind-vps`, and confirmed that the VPS is currently unreachable from this environment
- Verified: DNS still resolves `iindiinda.duckdns.org` to `89.22.226.34`; repeated `curl` checks to `/` and `/api/health` timed out; repeated SSH attempts to port `22` timed out
- Blockers: the VPS cannot currently be reached over HTTPS or SSH, so provider-panel reboot/console recovery is required before any Coolify log inspection or repair can continue
- Next: perform an out-of-band VPS reboot, then rerun only basic health checks (`ssh`, `systemctl`, `/`, `/api/health`) before deciding whether to inspect or abandon the paused Coolify install

## 2026-05-02 07:46 — Fresh VPS Manual Rebuild Restored

- Scope: rebuild the app from scratch on the reinstalled VPS and recover the manual HTTPS deployment baseline
- Changes: restored SSH key access, installed Nginx/MySQL/PHP/Composer/Certbot, cloned `front/ayan` into `/var/www/iind-app`, configured Laravel production env + database + migrations, rebuilt/deployed the Nuxt static frontend, restored HTTPS for `iindiinda.duckdns.org`, hardened Nginx asset handling, ran AYAN/AGAL live smoke flows with cleanup, restored `TELEGRAM_BOT_TOKEN`, refreshed Laravel caches, and verified the bot/menu configuration again
- Verified: `ssh iind-vps`; `systemctl is-active nginx php8.3-fpm mysql`; `php artisan migrate --force`; `php artisan route:list --path=api`; live `301` HTTP -> HTTPS; live `200` for `/`, `/ayan`, `/agal`, `/legal`, `/api/health`; guest protected API `401`; root HTML contains `apiBase:"/api"`; missing `/assets/*` returns `404`; AYAN + AGAL smoke lifecycle flows green; invalid `/api/auth/telegram` payload now returns `422 Telegram user data is invalid.`; bot API `getMe` resolves to `@iind_app_bot`; default menu button is `web_app` -> `https://iindiinda.duckdns.org/`
- Blockers: only one fresh real Telegram Mini App login retest is still missing on the rebuilt host
- Next: open the Mini App now, confirm login succeeds, and only inspect `/api/auth/telegram` immediately if that real retest fails

## 2026-05-02 11:10 — UUS First Real Source Slice

- Scope: start the next service track by moving UUS beyond concept/placeholder state into the first real MVP implementation slice in source
- Changes: added `vault/wiki/services/uus/api-contract.md`; rewrote `frontend/services/uus/README.md`; converted `frontend/services/uus/app/pages/uus.vue` into the required wrapper-only parent route; added `frontend/services/uus/app/pages/uus/index.vue`, `frontend/services/uus/app/pages/uus/task/[id].vue`, `frontend/services/uus/app/components/UusAccessState.vue`, `frontend/services/uus/app/components/UusCreateSlideover.vue`, `frontend/services/uus/app/types/uus.ts`, `frontend/services/uus/app/composables/useUusTasks.ts`, `frontend/services/uus/app/composables/useUusResponses.ts`, `frontend/services/uus/app/composables/useUusMy.ts`; added real backend UUS migrations/models/controllers/serializer/tests; replaced UUS route closures in `backend/routes/api.php`; added RU+SAH runtime copy for the new UUS flow
- Verified: `frontend npm run typecheck`; `frontend npm run build:static`; VPS PHP lint across all changed UUS backend files; isolated VPS temp backend copy with dev deps + separate MySQL test DB -> `./vendor/bin/phpunit tests/Feature/UusPersistenceTest.php` (`3 tests, 40 assertions`)
- Blockers: this UUS slice is still local/source-only and not deployed live yet
- Next: commit/push/deploy the UUS slice, then run live HTTPS + Telegram verification for create/respond/accept/finalize

## 2026-05-02 12:55 — UUS Live Deploy Green

- Scope: ship the first real UUS MVP slice to production and verify it on the restored manual VPS baseline
- Changes: committed `e25bb96`, pushed `front/ayan`, fast-forwarded VPS repo, ran UUS migrations, redeployed the frontend static bundle, live-smoked UUS API flows with cleanup, and merged the shipped branch back into `main`
- Verified: live `200` for `/uus` and `/uus/task/1`; guest `401` for `/api/uus/tasks`; root HTML still contains `apiBase:"/api"`; live UUS smoke passed create -> respond -> accept -> complete; accepted-response delete guard returns `422`; urgent-task 4th response blocked with `422 Response limit reached`; smoke users cleaned; `main` pushed at `0f638eb`
- Blockers: real manual Telegram/browser validation for UUS is still pending even though command-level live smoke is green
- Next: run a manual UUS pass, then decide whether to polish UUS further or start TAL

## 2026-05-02 15:20 — UUS UI Polish Pass

- Scope: polish the rough UUS Telegram UI after the user confirmed the live logic works
- Changes: redesigned the UUS feed/my-area/task/create surfaces, switched UUS cards to shared redesign primitives, added clearer task/response hierarchy, and cleaned unused Telegram transition-toggle state from AYAN/AGAL/UUS create slideovers
- Verified: `cd frontend && npx eslint services/uus/app/pages/uus/index.vue services/uus/app/pages/uus/task/[id].vue services/uus/app/components/UusCreateSlideover.vue services/ayan/app/components/AyanCreateSlideover.vue services/agal/app/components/AgalCreateSlideover.vue`; `cd frontend && npm run typecheck`; `cd frontend && npm run build:static`
- Blockers: local UI pass is not committed or deployed yet; final human approval in TMA/browser is still needed before ship
- Next: review the new UUS screens, then commit/push/deploy if accepted

## 2026-05-02 16:20 — UUS Tabs Deploy

- Scope: split the crowded UUS dashboard into clearer sections and ship the fix for real Telegram review
- Changes: rebuilt `frontend/services/uus/app/pages/uus/index.vue` around AYAN-like tabs + collapsible filters, added UUS tab/filter i18n copy, committed `5b23ae5`, pushed `front/ayan`, synced VPS, and redeployed the frontend static bundle
- Verified: focused eslint on changed UUS/AYAN/AGAL UI files; `cd frontend && npm run typecheck`; `cd frontend && npm run build:static`; VPS HEAD `5b23ae5`; live `200` for `/uus`, `/uus/task/1`, and `/api/health`; root HTML still contains `apiBase:"/api"`
- Blockers: waiting for one fresh Telegram visual pass on the new UUS tabs/filter layout
- Next: collect Telegram feedback, then decide whether more UUS polish is needed or TAL should start

## 2026-05-02 16:35 — UUS Task Detail Follow-Up Deploy

- Scope: fix the first user-reported post-deploy issues on the UUS task detail page without touching working business logic
- Changes: replaced the crooked responses counter badge with a dedicated counter pill, removed duplicated task meta rows from the detail card, added `tma-no-zoom` protection to the response form, blurred the active field before submit, committed `4216d98`, pushed `front/ayan`, synced VPS, and redeployed the frontend static bundle
- Verified: `cd frontend && npx eslint services/uus/app/pages/uus/task/[id].vue`; `cd frontend && npm run typecheck`; `cd frontend && npm run build:static`; VPS HEAD `4216d98`; live `200` for `/uus/task/1` and `/api/health`
- Blockers: one more real Telegram check is still needed to confirm the response-form zoom is truly gone on device
- Next: have the user retest the UUS task detail page in Telegram, then either close UUS polish or patch any remaining visual rough edges

## 2026-05-02 17:40 — TAL First Real Source Slice

- Scope: start TAL properly after the accepted UUS pass by replacing the old landing/showcase-only state with a first real availability + booking slice in source
- Changes: added the first TAL API contract, rewrote TAL README, converted `/tal` to wrapper + nested index/detail pages, added TAL access/role/create primitives plus TAL types/composables, wired real TAL backend models/controllers/migrations/tests/routes, and added RU+SAH copy for the new flow
- Verified: locale JSON parse; `cd frontend && npx eslint services/tal/app/pages/tal/index.vue services/tal/app/pages/tal/master/[id].vue services/tal/app/components/TalAccessState.vue services/tal/app/components/TalRoleSwitch.vue services/tal/app/components/TalCreateSlideover.vue services/tal/app/composables/useTalMasters.ts services/tal/app/composables/useTalBookings.ts services/tal/app/composables/useTalMy.ts services/tal/app/types/tal.ts`; `cd frontend && npm run typecheck`; `cd frontend && npm run build:static`
- Blockers: TAL slice is still local/source-only; backend runtime verification and any deploy are still pending
- Next: verify the TAL backend slice, then decide whether to ship the first TAL MVP slice live or patch source issues first

## 2026-05-02 18:00 — TAL First MVP Deploy

- Scope: verify, ship, and smoke the first real TAL availability + booking slice
- Changes: committed `ff0fedd`, pushed `front/ayan`, synced VPS, cleared stale Laravel route cache, ran TAL migrations, verified the backend in a temp VPS copy with dev deps plus isolated MySQL test DB, deployed the rebuilt frontend static bundle, and live-smoked the TAL create -> book -> accept -> complete path with cleanup
- Verified: VPS HEAD `ff0fedd`; `php artisan optimize:clear`; `php artisan migrate --force`; `php artisan route:list --path=api/tal`; `php -l` on changed TAL backend files; temp-copy `./vendor/bin/phpunit tests/Feature/TalPersistenceTest.php` (`3 tests, 36 assertions`); live `200` for `/tal`, `/tal/master/1`, `/api/health`; guest `401` for `/api/tal/masters`; accepted-booking delete guard returns `422`
- Blockers: one real Telegram visual pass is still needed on the shipped TAL flow
- Next: open TAL in Telegram, test create/book/accept UX, then decide whether more TAL polish is needed or fallback public requests should be the next TAL feature

## 2026-05-05 14:37 — Full Runtime Validation Green

- Scope: capture the user's new end-to-end Telegram validation result and shift the project stop point beyond MVP runtime bring-up
- Changes: recorded that the current AYAN/AGAL/UUS/TAL live runtime works excellently in Telegram, cleared the pending TAL visual-pass blocker, and updated sprint/resume/changelog planning toward launch-readiness or one deliberate post-MVP slice
- Verified: user-reported real Telegram Mini App validation on the current live runtime
- Blockers: runtime validation blocker is closed; remaining blockers are legal/compliance closure and limited local backend/container tooling
- Next: decide whether to enter launch-readiness/legal closure first or pick one intentionally small post-MVP product slice

## 2026-05-05 21:04 — Public Roadmap Packaging Pass

- Scope: package the validated MVP better for users and external viewers by exposing public roadmap state and refreshing the repository front page
- Changes: added `/roadmap`, added shared roadmap preview cards on AYAN/UUS/TAL/AGAL entry screens, added `usePublicRoadmap.ts`, refreshed root `README.md`, corrected TAL copy that implied an unshipped fallback flow, and updated vault docs/code map for the new slice
- Verified: locale JSON parse; focused eslint on changed frontend files; `cd frontend && npm run typecheck`; `cd frontend && npm run build:static`
- Blockers: this slice is still local/source-only and not deployed yet; launch-readiness/legal facts are still unresolved
- Next: review the packaging slice, then commit/push/deploy it if accepted before moving deeper into launch or post-MVP feature work

## 2026-05-05 21:22 — Roadmap Densified And Compacted

- Scope: add more future/improvement roadmap signal without turning the new roadmap surfaces into long text walls
- Changes: expanded roadmap items per service, changed shared roadmap cards to denser compact rendering with per-section counts, and limited service-entry previews to one visible item per section while keeping the full roadmap richer
- Verified: locale JSON parse; focused eslint on roadmap-related frontend files; `cd frontend && npm run typecheck`; `cd frontend && npm run build:static`
- Blockers: refined roadmap slice is still local/source-only and not deployed yet; launch-readiness/legal facts are still unresolved
- Next: review the refined roadmap slice, then commit/push/deploy it if accepted before moving deeper into launch or post-MVP feature work

## 2026-05-05 21:40 — Shared Access-State DRY Cleanup

- Scope: remove the duplicated Telegram/auth gate UI across service layers and replace it with one real shared component
- Changes: added shared `AppAccessState.vue`, converted AYAN/UUS/TAL/AGAL access-state components into thin wrappers, renamed the shared auth helper from AYAN-specific naming to generic naming, and updated all affected service pages/tests
- Verified: focused eslint on the shared access-state/auth files plus affected service pages; `cd frontend && npm run typecheck`; `cd frontend && npm run build:static`
- Blockers: this DRY cleanup is still local/source-only and not deployed yet; launch-readiness/legal facts are still unresolved
- Next: ship the current local packaging + DRY cleanup slice first, then continue launch-readiness or one deliberate post-MVP feature slice

## 2026-05-05 22:00 — Direct AppAccessState + Unified Copy

- Scope: finish the access-state cleanup properly by removing even the thin wrappers and unifying the copy for the identical Telegram/auth gate behavior
- Changes: switched all service pages to direct `AppAccessState`, deleted the service wrapper components, moved the text to one shared generic i18n block, removed the old per-service access-copy blocks, and updated docs accordingly
- Verified: locale JSON parse; focused eslint on shared access-state/auth files plus affected service pages; `cd frontend && npm run typecheck`; `cd frontend && npm run build:static`
- Blockers: this simplified DRY slice is still local/source-only and not deployed yet; launch-readiness/legal facts are still unresolved
- Next: review and ship the whole local packaging + direct shared access-state slice before deeper product/launch work
