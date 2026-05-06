# Resume Plan - 2026-05-06 13:20

> Goal: restart fast with exact stop point and no hidden chat memory.

## Frontend Cleanup Phase 1 Completed - 2026-05-06 13:20

- Executed the first cleanup phase from the frontend audit plan (hygiene + dead-path removal)
- Removed stale TAL legacy paths from runtime source:
  - `frontend/services/tal/app/pages/tal-showcase.vue`
  - `frontend/services/tal/app/composables/useTalAPI.ts`
  - `frontend/services/tal/app/composables/useTalStore.ts`
- Removed currently unused shared frontend files:
  - `frontend/app/components/AppHeader.vue`
  - `frontend/app/components/ErrorMessage.vue`
  - `frontend/app/composables/useNetwork.ts`
  - `frontend/app/composables/useStorage.ts`
  - `frontend/app/types/forms.ts`
  - `frontend/app/types/ui.ts`
  - `frontend/app/middleware/auth.ts`
- Removed deprecated npm config file `frontend/.npmrc` that produced repeated npm warnings
- Declared directly used missing deps in `frontend/package.json`:
  - `@internationalized/date`
  - `@nuxt/fonts`
- Synced lock metadata with `cd frontend && npm install --package-lock-only`
- Updated docs for this cleanup (`frontend/services/tal/README.md`, `vault/CODE_MAP.md`)
- Verification is green for this phase:
  - `cd frontend && npm run lint`
  - `cd frontend && npm run typecheck`
  - `cd frontend && npm run test`
  - `cd frontend && npm run build:static` (`STATIC_API_BASE_OK`)

## Theme Variable Syntax Fix Deployed - 2026-05-05 22:23

- User spotted invalid frontend color syntax like `rgb(var(--color-cyan-400))`
- Root cause confirmed: these theme variables are hex values, not RGB channel triplets, so `rgb(var(--color-...))` is invalid
- Fixed all remaining source occurrences under `frontend/` and replaced them with direct `var(--color-...)` usage
- Shipped runtime/frontend commit for this follow-up: `ae2b0a9` `fix(ui): use theme vars directly`
- Verification is green:
  - `rg -n "rgb\\(var\\(--color-[^)]+\\)\\)" frontend -S` -> no source matches
  - `cd frontend && npx eslint app/components/AppHeader.vue app/components/AppBottomNav.vue app/components/AppTitle.vue app/components/AppServiceAbout.vue app/components/ServiceCard.vue app/components/LoadingSpinner.vue app/components/EmptyState.vue app/components/ErrorMessage.vue`
  - `cd frontend && npm run typecheck`
  - `cd frontend && npm run build:static`
  - `git push origin front/ayan`
  - `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` -> `ae2b0a9`
  - live `200` for `/`, `/roadmap`, and `/api/health`
- Important deploy note: the manual VPS rollout still preserves older hashed assets in `frontend/public/assets` for cache compatibility, so recursive grep on the whole deployed assets directory can still hit older stale files even though the current source and current shipped bundle are fixed

## Public Packaging Slice Deployed - 2026-05-05 22:17

- Committed the local packaging + DRY cleanup slice as `ee4b71c` `feat(app): add roadmap and unify access gate`
- Pushed `front/ayan` and fast-forwarded the VPS checkout to the same commit
- Deployed the rebuilt frontend static bundle to VPS via the safe `public_new -> public` swap flow
- Current live frontend now includes:
  - `/roadmap`
  - compact service-entry roadmap previews on AYAN/UUS/TAL/AGAL
  - refreshed root `README.md` in source
  - one direct shared `AppAccessState` gate with unified generic copy
- Verification is green after the deploy:
  - `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` -> `ee4b71c`
  - live `200` for `/`, `/roadmap`, `/ayan`, and `/api/health`
  - live root HTML still contains `apiBase:"/api"`
- Local, origin, and VPS are aligned again at this stop point
- Local working tree is clean again at this stop point

## Shared Access-State DRY Cleanup - 2026-05-05 22:00

- Removed the duplicated Telegram/auth access-state UI logic across AYAN, UUS, TAL, and AGAL source
- Added shared `frontend/app/components/AppAccessState.vue` as the single main access gate component
- Removed the service-specific access-state wrapper components entirely and switched all affected pages to call `AppAccessState` directly
- Unified the copy too: the access gate now uses one shared generic i18n block instead of four different service-specific text variants for the same behavior
- Renamed the shared auth helper naming from AYAN-specific to generic source-of-truth naming:
  - `AyanAccessState` -> `ServiceAccessState`
  - `getAyanAccessState()` -> `getServiceAccessState()`
- Updated all affected service pages, `frontend/services/tal/README.md`, and the auth unit test to the new shared helper/component shape
- Verification is green after the DRY cleanup:
  - locale JSON parse for `frontend/i18n/locales/{ru,sah}.json`
  - `cd frontend && npx eslint app/components/AppAccessState.vue app/utils/auth.ts tests/unit/auth.test.ts services/ayan/app/pages/ayan/index.vue services/ayan/app/pages/ayan/trip/[id].vue services/ayan/app/pages/ayan/request/[id].vue services/uus/app/pages/uus/index.vue services/uus/app/pages/uus/task/[id].vue services/tal/app/pages/tal/index.vue services/tal/app/pages/tal/master/[id].vue services/agal/app/pages/agal/index.vue services/agal/app/pages/agal/route/[id].vue services/agal/app/pages/agal/request/[id].vue`
  - `cd frontend && npm run typecheck`
  - `cd frontend && npm run build:static`
- This cleanup is still local/source-only together with the broader roadmap/README packaging slice: not committed, not pushed, and not deployed yet

## Roadmap Densified And Compacted - 2026-05-05 21:22

- The new public roadmap slice is no longer only a first pass; it was refined to carry more future signal while staying compact on mobile
- Each service now has richer `improving now` + `planned next` coverage in source
- Shared roadmap cards now show dense title-first lists with per-section counts
- Service entry previews are intentionally limited to one visible item per section so the service pages stay compact
- The shared `/roadmap` page keeps the fuller multi-item view across AYAN, UUS, TAL, and AGAL
- Verification is green again after this refinement:
  - locale JSON parse for `frontend/i18n/locales/{ru,sah}.json`
  - `cd frontend && npx eslint app/pages/index.vue app/pages/roadmap.vue app/components/AppRoadmapCard.vue app/composables/usePublicRoadmap.ts services/ayan/app/pages/ayan/index.vue services/uus/app/pages/uus/index.vue services/tal/app/pages/tal/index.vue services/agal/app/pages/agal/index.vue`
  - `cd frontend && npm run typecheck`
  - `cd frontend && npm run build:static`
- This refined roadmap slice is still local/source-only at this stop point: not committed, not pushed, and not deployed yet

## Public Roadmap Packaging Pass - 2026-05-05 21:04

- Source now includes a public-facing packaging slice so users can see what is live, what is being improved, and what is planned next
- Added a new public `/roadmap` route in the shared Nuxt app shell
- Added shared roadmap preview cards to the AYAN, UUS, TAL, and AGAL entry screens
- Refreshed the root `README.md` so the repository front page reflects the real current product state instead of the older scaffold-era framing
- Corrected TAL user-facing copy so it no longer implies that public fallback client requests already exist in live scope
- Verification is green in local source:
  - locale JSON parse for `frontend/i18n/locales/{ru,sah}.json`
  - `cd frontend && npx eslint app/pages/index.vue app/pages/roadmap.vue app/components/AppRoadmapCard.vue app/composables/usePublicRoadmap.ts services/ayan/app/pages/ayan/index.vue services/uus/app/pages/uus/index.vue services/tal/app/pages/tal/index.vue services/agal/app/pages/agal/index.vue`
  - `cd frontend && npm run typecheck`
  - `cd frontend && npm run build:static`
- This packaging pass is still local/source-only at this stop point: not committed, not pushed, and not deployed yet

## Full Runtime Validation Green - 2026-05-05 14:37

- User manually rechecked the live Telegram Mini App and reported that the current project works excellently
- This closes the previously pending TAL real-device visual-pass blocker
- Current live runtime can now be treated as validated across the implemented service tracks:
  1. AYAN
  2. AGAL
  3. UUS
  4. TAL
- Current product state is now effectively MVP-complete for runtime/UI behavior on the implemented scope
- Next decision is now strategic rather than defect-driven:
  1. launch-readiness / legal / compliance closure
  2. a small polish pass only if new real usage feedback appears
  3. one intentionally chosen post-MVP feature slice

## TAL First MVP Deploy - 2026-05-02 18:00

- User accepted the current UUS pass well enough to move to TAL next
- TAL is no longer only a landing/showcase concept; the first real availability + booking slice is now shipped live as `ff0fedd` `feat(tal): add booking MVP slice`
- Current TAL live scope is intentionally the availability-core flow only:
  1. master publishes an availability card
  2. client sends a booking request
  3. master accepts or rejects
  4. Telegram contact is revealed after acceptance
- Public fallback client requests are intentionally deferred for a later TAL pass
- VPS backend needed one important post-pull fix before verification/deploy was truly green:
  - `php artisan optimize:clear` was required so stale cached routes stopped hiding the new TAL endpoints
- Verification is now green across source + VPS runtime:
  - locale JSON parse for `frontend/i18n/locales/{ru,sah}.json`
  - `cd frontend && npx eslint services/tal/app/pages/tal/index.vue services/tal/app/pages/tal/master/[id].vue services/tal/app/components/TalAccessState.vue services/tal/app/components/TalRoleSwitch.vue services/tal/app/components/TalCreateSlideover.vue services/tal/app/composables/useTalMasters.ts services/tal/app/composables/useTalBookings.ts services/tal/app/composables/useTalMy.ts services/tal/app/types/tal.ts`
  - `cd frontend && npm run typecheck`
  - `cd frontend && npm run build:static`
  - `git push origin front/ayan`
  - VPS checkout fast-forward to `ff0fedd`
  - `php artisan migrate --force` added TAL master + booking tables
  - `php artisan route:list --path=api/tal` now shows `10` TAL routes
  - temp VPS backend copy + isolated MySQL test DB -> `./vendor/bin/phpunit tests/Feature/TalPersistenceTest.php` ✅ (`3 tests, 36 assertions`)
  - live TAL synthetic smoke passed: create availability -> book -> accept -> complete; accepted-booking delete returns `422`
  - live `200` for `/tal`, `/tal/master/1`, and `/api/health`; guest `401` for `/api/tal/masters`

## TAL First Real Source Slice - 2026-05-02 17:40

- User accepted the current UUS pass well enough to move to TAL next
- TAL is no longer only a landing/showcase concept in local source
- Source now contains the first real TAL availability-core slice:
  1. master publishes an availability card
  2. client sends a booking request
  3. master accepts or rejects
  4. Telegram contact is revealed after acceptance
- Implemented source coverage includes:
  - `frontend/services/tal/app/pages/tal.vue`
  - `frontend/services/tal/app/pages/tal/index.vue`
  - `frontend/services/tal/app/pages/tal/master/[id].vue`
  - `frontend/services/tal/app/components/TalAccessState.vue`
  - `frontend/services/tal/app/components/TalRoleSwitch.vue`
  - `frontend/services/tal/app/components/TalCreateSlideover.vue`
  - `frontend/services/tal/app/types/tal.ts`
  - `frontend/services/tal/app/composables/useTalMasters.ts`
  - `frontend/services/tal/app/composables/useTalBookings.ts`
  - `frontend/services/tal/app/composables/useTalMy.ts`
  - `backend/app/Http/Controllers/Tal/*`
  - `backend/app/Models/TalMaster.php`
  - `backend/app/Models/TalBooking.php`
  - `backend/database/migrations/2026_05_02_170000_create_tal_masters_table.php`
  - `backend/database/migrations/2026_05_02_170001_create_tal_bookings_table.php`
  - `backend/tests/Feature/TalPersistenceTest.php`
  - `vault/wiki/services/tal/api-contract.md`
- Important scope decision: public fallback client requests are intentionally deferred for a later TAL pass; this first slice ships availability + booking only
- Verification currently green on frontend/source side:
  - locale JSON parse for `frontend/i18n/locales/{ru,sah}.json`
  - `cd frontend && npx eslint services/tal/app/pages/tal/index.vue services/tal/app/pages/tal/master/[id].vue services/tal/app/components/TalAccessState.vue services/tal/app/components/TalRoleSwitch.vue services/tal/app/components/TalCreateSlideover.vue services/tal/app/composables/useTalMasters.ts services/tal/app/composables/useTalBookings.ts services/tal/app/composables/useTalMy.ts services/tal/app/types/tal.ts`
  - `cd frontend && npm run typecheck`
  - `cd frontend && npm run build:static`
- This TAL slice is still local/source-only and not committed, pushed, or deployed yet

## UUS Task Detail Follow-Up - 2026-05-02 16:35

- User checked the shipped UUS tabs deploy in Telegram and said the overall direction is good, but reported three specific task-detail issues:
  1. owner responses counter looked visually crooked (`1/3` alignment)
  2. task detail repeated top-level meta unnecessarily
  3. the inline response form could still trigger disruptive zoom while typing/submitting
- Source/runtime follow-up is now shipped in `4216d98` `fix(uus): refine task response ui`
- `frontend/services/uus/app/pages/uus/task/[id].vue` now:
  1. uses a dedicated counter pill for the owner responses count
  2. keeps `when` / `budget` primarily in the hero chips instead of repeating them in the detail card
  3. wraps the inline response form in `tma-no-zoom` protection and blurs the active field before submit
- This pass still avoids Telegram-specific slideover transition gating like `:transition="!isInTelegram"`
- Verification is green:
  - `cd frontend && npx eslint services/uus/app/pages/uus/task/[id].vue`
  - `cd frontend && npm run typecheck`
  - `cd frontend && npm run build:static`
  - `git push origin front/ayan`
  - VPS checkout fast-forward to `4216d98`
  - live `200` for `/uus/task/1` and `/api/health`

## UUS Tabs Deploy Update - 2026-05-02 16:20

- User had already confirmed that the live UUS logic works in Telegram Mini App; main remaining feedback was the crowded dashboard layout
- UUS dashboard is now shipped with AYAN-like sectioning instead of stacking everything on one page:
  1. tabs for `open tasks` / `my tasks` / `my responses`
  2. collapsible filter panel with active-filter count
  3. create CTA below filters
- This pass still avoids Telegram-specific slideover transition gating like `:transition="!isInTelegram"`
- AYAN, AGAL, and UUS create slideovers also had the now-unused `isInTelegram` destructuring cleaned up in source
- Shipped runtime/frontend commit for this pass: `5b23ae5` `feat(uus): polish dashboard tabs`
- Verification is green:
  - `cd frontend && npx eslint services/uus/app/pages/uus/index.vue services/uus/app/pages/uus/task/[id].vue services/uus/app/components/UusCreateSlideover.vue services/ayan/app/components/AyanCreateSlideover.vue services/agal/app/components/AgalCreateSlideover.vue`
  - `cd frontend && npm run typecheck`
  - `cd frontend && npm run build:static`
  - `git push origin front/ayan`
  - VPS checkout fast-forward to `5b23ae5`
  - live `200` for `/uus`, `/uus/task/1`, and `/api/health`
  - live root HTML still contains `apiBase:"/api"`

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

## Recovery Recheck - 2026-04-29 13:36

- Resumed the paused Coolify recovery plan with read-only health checks first
- DNS still resolves `iindiinda.duckdns.org` to `89.22.226.34`
- Current production host is worse than the prior intermittent state:
  1. repeated `curl` checks to `https://iindiinda.duckdns.org/` timed out
  2. repeated `curl` checks to `https://iindiinda.duckdns.org/api/health` timed out
  3. repeated SSH attempts to `iind-vps` timed out on port `22`
- Because the VPS is unreachable, the planned inspection of `/data/coolify/source/*.log`, `systemctl`, Docker DNS, and daemon config could not be performed from this environment
- Next mandatory move is now out-of-band recovery through the VPS/provider panel or console before any more Coolify work

## Infrastructure Reset - 2026-05-02 07:46

- The VPS reinstall path is now complete enough to treat the fresh host as the new live baseline again
- SSH automation works again through `iind-vps`
- The rebuilt host now runs the intended manual deployment topology:
  1. host-managed `nginx + php8.3-fpm + mysql`
  2. Laravel backend in `/var/www/iind-app/backend`
  3. Nuxt static frontend in `/var/www/iind-app/frontend/public`
  4. same-origin `/api` through Nginx
  5. HTTPS restored for `iindiinda.duckdns.org`
- Source branch on the rebuilt VPS is `front/ayan` at `f1d1f5d`
- Restore scope now live again on the rebuilt host:
  1. AYAN real backend + frontend
  2. AGAL real backend + frontend
  3. legal center/routes
  4. UUS/TAL landing pages only
- New Nginx baseline also includes static hardening:
  - missing `/assets/*` returns `404`
  - `index.html` is `no-store`
  - hashed assets/fonts are cacheable
- Telegram runtime secret is restored on the rebuilt host
- Bot API verification is green again:
  - `getMe` resolves to `@iind_app_bot`
  - default menu button is `web_app`
  - menu URL is `https://iindiinda.duckdns.org/`
- Backend auth endpoint now follows the Telegram validation path again (`422 Telegram user data is invalid.` for an invalid payload)
- User rechecked the rebuilt runtime in Telegram and reported that everything works again on the fresh host

## Stop Point

- Current branch: `front/ayan`
- User now reports that the current live runtime works excellently in Telegram Mini App, including the TAL flow
- User-reported manual UUS Telegram validation is green for the core create/respond/accept/finalize flow
- Current live frontend packaging/runtime commit is now `ae2b0a9` `fix(ui): use theme vars directly`
- Current live UUS frontend still includes the dashboard split into tabs plus collapsible filters plus the task-detail counter/no-zoom follow-up
- Current live TAL frontend now includes `/tal` feed tabs + filters + create status flow and `/tal/master/[id]` booking detail flow
- Current local working tree is clean again at session close
- Local, origin, and VPS are aligned again after the later theme-variable follow-up deploy
- `main` now also includes the UUS slice plus the latest vault sync via merge commit `c12330c`
- UUS first real MVP slice is now committed, pushed, deployed, and live-smoked:
  - real backend persistence for `tasks`, `responses`, `my/*`, serializer, models, migrations, and PHPUnit coverage
  - real frontend feed/create/detail/my-area under `frontend/services/uus/app/`
  - UUS page structure follows the mandatory wrapper + nested index pattern
- Fresh VPS manual deployment baseline is restored and reachable on `https://iindiinda.duckdns.org`
- Live route checks are green again for `/`, `/roadmap`, `/ayan`, `/agal`, `/tal`, `/legal`, and `/api/health`
- Live guest auth gate is green again (`401` on protected API)
- Live AYAN + AGAL API smoke is green again after rebuild:
  - AYAN trip flow `accepted -> completed`
  - AYAN request flow `accepted -> cancelled`
  - AGAL route flow `accepted -> completed`
  - AGAL request flow `accepted -> cancelled`
- Synthetic smoke records/tokens were cleaned back out of MySQL after verification
- Fresh manual runtime baseline is now green again in real Telegram use, not only command-level smoke
- Live UUS HTTPS/API smoke is green after deploy
- Live TAL HTTPS/API smoke is now also green after deploy
- Coolify remains paused and should stay paused while the manual baseline is now healthy again
- Latest live AYAN runtime behavior remains green after the redesign deployment and the older `5e81817` create-form simplification still remains part of the stable baseline
- AGAL backend persistence is shipped on VPS and the redesigned frontend slice is now live on `/agal` (feed, create, detail, respond, contact reveal, lifecycle actions)
- User completed manual Telegram Mini App testing and reported AYAN works end-to-end well enough for MVP acceptance
- Live frontend bundle is redeployed with collapsed-by-default service explainers on AYAN, UUS, TAL, and AGAL entry screens
- Legal docs now render via `rt()` on live build and legal navigation is reduced to the home bottom card only
- Live frontend bundle is redeployed with corrected same-origin `apiBase:"/api"`
- Latest auth hardening code commit is `af93b9b` `fix(auth): harden tma bootstrap`
- Static deploy prevention now exists in source via guarded `npm run build:static`
- Source now also includes a first-pass Coolify deployment starter layout:
  - `docker-compose.coolify.yml`
  - `frontend/Dockerfile.coolify`
  - `backend/Dockerfile.coolify`
  - `backend/docker/entrypoint.coolify.sh`
  - `ops/coolify/frontend.nginx.conf`
  - `ops/coolify/backend.nginx.conf`
  - `ops/coolify/README.md`
  - `ops/coolify/SETUP.md` (exact operator setup guide)
- Real production-VPS Coolify attempt was started and then paused:
  - installer source state exists under `/data/coolify/source`
  - first hard failure captured: Docker pull of `ghcr.io/coollabsio/coolify:4.0.0-beta.474` hit `lookup ghcr.io: i/o timeout`
  - Docker cleanup reclaimed about `977MB`, increasing free disk to about `5.2GB`
  - host still has only about `10GB` total disk, far below Coolify's recommended comfort zone
  - SSH/HTTP were intermittent during install/restart windows, so no more night-time recovery actions should be attempted blindly
  - latest recheck after pausing still showed degraded health: SSH only worked intermittently after several retries, while repeated HTTPS root checks timed out
- Root `DESIGN.md` now exists as the redesign baseline for colors, typography, spacing, rounding, and shared component patterns
- Redesigned bottom nav now highlights the tapped service immediately on first tap via optimistic pending-route state
- Commit `bc7bdc4` is the saved redesign variant 1 checkpoint
- Commit `b22f92c` is the saved redesign variant 2 checkpoint
- Redesign variant 3 is now the active live frontend runtime direction: home, landing, feed, detail, and create surfaces follow the same calmer daily-use styling
- `iind` remains the cyan brand anchor and the literal home `iindiinda` reminder was removed
- Latest shipped frontend runtime commit is `ae2b0a9` `fix(ui): use theme vars directly`
- Live deployment baseline is again healthy at `https://iindiinda.duckdns.org` after the VPS rebuild, the UUS passes, and the TAL deploy
- Verified live routes (`200`):
  - `/`
  - `/ayan`
  - `/tal`
  - `/tal/master/1`
  - `/legal`
  - `/legal/ayan-terms`
  - `/legal/uus-rules`
  - `/legal/tal-rules`
  - `/legal/agal-rules`
  - `/api/health`
- Backend migrations are now fully applied, including:
  - `2026_04_24_120000_expand_ayan_target_statuses`
  - `2026_04_26_071000_create_agal_routes_table`
  - `2026_04_26_071001_create_agal_requests_table`
  - `2026_04_26_071002_create_agal_responses_table`
  - `2026_05_02_170000_create_tal_masters_table`
  - `2026_05_02_170001_create_tal_bookings_table`
- Live lifecycle API smoke is green for both AYAN trip and request target flows

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
  - focused eslint on current UUS/AYAN/AGAL UI files ✅
  - `npm run build:static` ✅
  - `JSON.parse(frontend/i18n/locales/ru.json)` ✅
  - `JSON.parse(frontend/i18n/locales/sah.json)` ✅
  - `npx nuxt build --preset github_pages` + `node scripts/verify-static-api-base.mjs` ✅
- Backend (VPS checkout / temp test copy):
  - `./vendor/bin/phpunit tests/Feature/AuthApiTest.php tests/Feature/AyanAuthTest.php tests/Feature/AyanPersistenceTest.php` ✅ (`16 tests, 127 assertions`)
  - `./vendor/bin/phpunit tests/Feature/AgalPersistenceTest.php` ✅ (`4 tests, 60 assertions`)
  - temp copy `./vendor/bin/phpunit tests/Feature/TalPersistenceTest.php` ✅ (`3 tests, 36 assertions`)
- Runtime:
  - `ssh iind-vps "git -C /var/www/iind-app rev-parse --short HEAD"` ✅ (`ff0fedd` before later vault sync)
  - `curl -I https://iindiinda.duckdns.org/` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/ayan` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/agal` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/uus` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/uus/task/1` ✅ (`200` SPA route fallback)
  - `curl -I https://iindiinda.duckdns.org/tal` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/tal/master/1` ✅ (`200` SPA route fallback)
  - `curl -I https://iindiinda.duckdns.org/legal/ayan-terms` ✅ (`200`)
  - `curl -I https://iindiinda.duckdns.org/api/health` ✅ (`200`)
  - `curl -s -o /dev/null -w "%{http_code}" https://iindiinda.duckdns.org/api/agal/routes` ✅ (`401` guest auth gate expected)
  - `curl -s -o /dev/null -w "%{http_code}" https://iindiinda.duckdns.org/api/tal/masters` ✅ (`401` guest auth gate expected)
  - live TAL synthetic smoke ✅ (`create -> book -> accept -> complete`, accepted delete -> `422`)
  - `curl https://iindiinda.duckdns.org/` contains `apiBase:"/api"` ✅

## Next Action

1. Use the newly shipped roadmap surface for soft-launch conversations, user expectation-setting, and partner outreach
2. Capture the next high-value non-code layer while context is still hot:
   - go-to-market / distribution plan
   - ideal user and buyer profiles
   - launch checklist
3. Then keep the next main strategic choice explicit:
   - launch-readiness / legal / compliance closure
   - or one intentionally small post-MVP feature slice
4. If launch-readiness comes first, gather and lock the missing factual inputs before touching legal texts again:
   - operator identity and requisites
   - support email/address
   - hosting / localization facts
   - retention periods
5. Keep manual VPS deployment as the baseline and keep Coolify paused

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
Current task: turn the now-shipped MVP into a clearer launch/distribution plan.
1) treat commit ee4b71c on front/ayan as the live baseline with the roadmap/README/access-gate packaging already deployed
2) do not reopen speculative UI rewrites or infra experiments first
3) write the next non-code docs while context is still hot: go-to-market, ideal user/buyer profiles, and launch checklist
4) keep the legal-readiness path explicit by listing the still-missing operator/support/hosting/retention facts
5) keep manual VPS deployment as the baseline and keep Coolify paused
```

## Deployment Context

- VPS frontend static directory: `/var/www/iind-app/frontend/public`
- VPS backend repository: `/var/www/iind-app`
- Nginx tracked config: `ops/nginx/iind-vps-default.conf`
- Live domain: `https://iindiinda.duckdns.org`

## One-Line Summary

Current live AYAN/AGAL/UUS/TAL runtime is user-validated and now also ships the public roadmap/README/access-gate packaging on commit `ee4b71c`; next step is launch/distribution planning plus legal-readiness decisions, not more speculative runtime churn.
