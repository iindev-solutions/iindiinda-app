# Sprint - Phase 1 AYAN MVP

> Roadmap: `vault/wiki/architecture/roadmap.md`
> API contract: `vault/wiki/services/ayan/api-contract.md`
> Code map: `vault/CODE_MAP.md`

## Scope

Start date: `2026-04-19`

Goal: ship a working AYAN MVP flow:

1. create trip or ride request
2. browse feed with filters
3. respond
4. exchange contact details

## Resume Point - 2026-04-26 09:00

- Current branch: `front/ayan`
- Latest live AYAN runtime code still includes the create-form simplification commit `5e81817` (`fix(ayan): simplify tma create form`)
- Local, origin, and VPS repositories are aligned on the current branch tip
- Live HTTPS runtime is available at `https://iindiinda.duckdns.org`
- Legal routes are deployed and reachable on live HTTPS
- Lifecycle statuses (`matched/completed/cancelled`) are deployed on backend + frontend
- Live synthetic API smoke for lifecycle transitions is green
- User-reported real-device Telegram Mini App verification is green for AYAN MVP scope
- Continue from: `vault/resume-plan.md`

## Current Reality

- Fresh VPS manual rebuild is now live again on `https://iindiinda.duckdns.org`
- Current live restore scope now includes four real service/runtime tracks:
  - AYAN real backend + frontend
  - AGAL real backend + frontend
  - UUS first real backend + frontend MVP slice
  - TAL first real backend + frontend MVP slice
  - legal center/routes
- The rebuilt host now runs the manual deployment baseline again:
  - `nginx + php8.3-fpm + mysql`
  - Laravel backend in `/var/www/iind-app/backend`
  - Nuxt static frontend in `/var/www/iind-app/frontend/public`
  - same-origin `/api`
- SSH automation via `iind-vps` is restored
- HTTPS is restored and live route checks are green again
- Live AYAN + AGAL lifecycle smoke is green again after the rebuild
- Live UUS smoke is now also green after the first deploy
- User reported that the rebuilt Telegram Mini App runtime works again
- User already reported that the live UUS logic works in Telegram Mini App well enough to move on
- User has now manually rechecked the live TAL flow too and reports that the broader current runtime works excellently in Telegram
- Public roadmap packaging is now also shipped live: `/roadmap`, compact per-service roadmap preview cards, a refreshed root `README.md`, and one shared direct `AppAccessState` gate with unified copy
- Nginx static handling is hardened so missing `/assets/*` returns `404`
- Coolify remains explicitly paused

## Sprint Tasks

| # | Task | Status | Blockers |
|---|------|--------|----------|
| 1.1 | Backend migrations (`users`, `trips`, `requests`, `responses`) | DONE | - |
| 1.2 | Backend AYAN models and controllers | DONE | - |
| 1.3 | Frontend AYAN structure (`pages`, `composables`, `types`) | DONE | - |
| 1.4 | Frontend create flow (`slideover`, form switching) | DONE | - |
| 1.5 | Frontend feed, tabs, filters, empty states | DONE | - |
| 1.6 | Frontend performance pass (`lazy`, `useLazyAsyncData`) | DONE | - |
| 1.7 | Response flow, contact link, accept/reject | DONE | - |
| 1.8 | Feed filters by route/date | DONE | - |
| 1.9 | Nuxt UI color alignment | DONE | - |
| 1.10 | Integration manual Telegram/browser verification | DONE | user-reported real-device pass green |
| 1.11 | Legal pages + footer/access legal links | DONE | shipped in `f13f6b6` |
| 1.12 | Legal-only commit + VPS deploy | DONE | live on `https://iindiinda.duckdns.org` |
| 1.13 | Lifecycle status expansion for targets/responses | DONE | shipped in `a3591a0` |
| 1.14 | QA complete E2E verification | DONE | user-reported AYAN MVP flow green |

## Active Blockers

- No local `php`, `composer`, or `docker` in this environment for full backend execution outside the VPS
- RF legal closure is still blocked by unresolved personal-data localization and final operator disclosure details

## Decisions Already Taken

- Use `useLazyAsyncData` for AYAN pages
- Keep app dark-only with Nuxt UI primary/neutral colors
- Keep browser flow TMA-first for production auth behavior
- Ship legal pack first as isolated deploy, then continue lifecycle feature work

## Next Practical Step

1. Use the newly shipped roadmap/README packaging to support soft-launch conversations, partner outreach, and early-user onboarding
2. Capture the next non-code product layer while context is still hot:
   - go-to-market / distribution plan
   - ideal user and buyer profiles
   - launch checklist
3. In parallel, keep the main strategic choice explicit:
   - launch-readiness / legal / compliance closure
   - or one intentionally small post-MVP feature slice driven by real demand
4. If launch-readiness comes first, gather the missing operator/support/hosting/retention facts before editing legal copy further
5. Keep manual VPS deployment as the only target and keep Coolify paused

## Definition Of Progress For This Sprint

This sprint is complete only when:

- AYAN flows work against real backend on live HTTPS deployment
- legal pages and links are committed and live in production
- lifecycle status expansion is verified and deployed as a separate safe slice
- `vault/` contains an exact stop point with no hidden chat-only state

## Sprint Status

- AYAN MVP sprint can now be treated as complete for runtime/UI scope based on green live API smoke plus user-reported real-device Telegram Mini App verification
- Remaining legal/compliance work is still important, but it is no longer blocking the AYAN MVP runtime handoff
- AGAL remains the newest implemented service track and now has both shipped backend persistence and shipped frontend MVP UI on VPS: feed, filters, role switching, create flow, detail pages, respond flow, contact reveal, and lifecycle actions
- User has now also manually validated the shipped TAL availability + booking slice, so all four current service tracks are green in real Telegram use
- Immediate next execution target is no longer service-by-service runtime validation; the clearer public packaging for the validated MVP is already shipped, and the next highest-value work is launch/distribution planning plus legal-readiness decisions rather than more speculative UI churn
- Root `DESIGN.md` now exists as the shared redesign baseline and lint passes cleanly
- Commit `bc7bdc4` locks redesign variant 1, commit `b22f92c` locks redesign variant 2, and the current local working tree continues variant 3 as the active chosen direction
- Variant 3 now covers home, service landing pages, feed screens, detail pages, and create slideovers and is deployed live on the frontend runtime
- A first-pass Coolify deployment layout now exists in source as an alternative to the current manual VPS deployment flow, but it is not trial-verified yet
- Remaining redesign work is now lower priority than restoring VPS reachability and deciding whether this host should carry any Coolify ambition at all
