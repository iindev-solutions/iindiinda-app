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

- Frontend AYAN uses real API (`/api`) on the same VPS host
- Backend AYAN runtime is live on VPS with Laravel + Sanctum + MySQL
- HTTPS and HTTP -> HTTPS redirect are active for DuckDNS domain
- `TELEGRAM_BOT_TOKEN` is present on VPS backend env (prior blocker fixed)
- Role switch UI, detail back button, status cards, and Telegram-safe create form are deployed
- Legal pages/links are now committed, pushed, and deployed
- Lifecycle status expansion (`matched/completed/cancelled`) is now committed, pushed, migrated, and deployed
- Production API behavior for lifecycle states is validated (`open -> matched -> completed/cancelled`)
- Platform-wide legal-center expansion is now deployed live; do not treat it as final legal closure until operator details and RF data-localization posture are resolved
- AGAL backend persistence slice is live on VPS, and the first real AGAL frontend create/feed/detail slice is now also deployed live

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

- VPS DB migration flow was recovered after a historical partial apply; monitor schema health in next deploy window
- No local `php`, `composer`, or `docker` in this environment for full backend execution
- RF legal closure is still blocked by unresolved personal-data localization and final operator disclosure details

## Decisions Already Taken

- Use `useLazyAsyncData` for AYAN pages
- Keep app dark-only with Nuxt UI primary/neutral colors
- Keep browser flow TMA-first for production auth behavior
- Ship legal pack first as isolated deploy, then continue lifecycle feature work

## Next Practical Step

1. Shared redesign baseline now exists in root `DESIGN.md`
2. Use `DESIGN.md` plus the current AYAN + AGAL runtime behavior as the implementation baseline
3. Implement shared frontend shell and design-system primitives next:
   - page shell
   - service cards
   - content cards
   - buttons
   - inputs
   - tabs
   - lifecycle/status badges
4. After primitives land, redesign home + service landing pages, then feed/detail/create surfaces without unnecessary backend churn

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
- Immediate next execution target is no longer deeper AGAL feature work; it is a project redesign started while the UI surface is still small enough to change safely
- Root `DESIGN.md` now exists as the shared redesign baseline and lint passes cleanly
- First redesign implementation slice is now in source for shared shell, home, UUS/TAL landing pages, and AYAN/AGAL entry-feed screens; next redesign work should move to detail/create surfaces
