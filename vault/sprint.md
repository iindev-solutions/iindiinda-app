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

## Resume Point - 2026-04-26 06:00

- Current branch: `front/ayan`
- Latest live runtime code includes the AYAN create-form simplification commit `5e81817` (`fix(ayan): simplify tma create form`)
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

1. Post-MVP product direction is now chosen: AGAL goes next while legal waits
2. Use `vault/wiki/services/agal/api-contract.md` as the implementation source of truth for AGAL MVP
3. First AGAL implementation slice:
   - align backend placeholder routes to the AYAN-like dual-surface model
   - scaffold AGAL frontend structure to mirror AYAN
   - keep scope to create/feed/respond/contact only
4. Keep AYAN in maintenance mode and patch only if new runtime regressions appear

## Definition Of Progress For This Sprint

This sprint is complete only when:

- AYAN flows work against real backend on live HTTPS deployment
- legal pages and links are committed and live in production
- lifecycle status expansion is verified and deployed as a separate safe slice
- `vault/` contains an exact stop point with no hidden chat-only state

## Sprint Status

- AYAN MVP sprint can now be treated as complete for runtime/UI scope based on green live API smoke plus user-reported real-device Telegram Mini App verification
- Remaining legal/compliance work is still important, but it is no longer blocking the AYAN MVP runtime handoff
- Next execution target is AGAL MVP because it offers the highest AYAN architecture reuse among the remaining service tracks
- AGAL scaffold slice has now started and is deployed live: contract locked, backend route shape aligned, frontend structure/pages/composables/types created
