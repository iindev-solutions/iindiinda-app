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

## Resume Point - 2026-04-24 20:15

- Current branch: `front/ayan`
- Latest shipped commit is `a3591a0` (`feat(ayan): expand trip/request lifecycle statuses`)
- Current branch tip for handoff docs/sync is `d019d0c`
- Live HTTPS runtime is available at `https://iindiinda.duckdns.org`
- Legal routes (`/legal/ayan-terms`, `/legal/privacy`, `/legal/ayan-safety`) are deployed and reachable on live HTTPS
- Lifecycle statuses (`matched/completed/cancelled`) are now deployed on backend + frontend
- Live synthetic API smoke for lifecycle transitions is completed and green
- Local, origin, and VPS repo heads are aligned at `d019d0c`
- Continue from: `vault/resume-plan.md`

## Current Reality

- Frontend AYAN uses real API (`/api`) on the same VPS host
- Backend AYAN runtime is live on VPS with Laravel + Sanctum + MySQL
- HTTPS and HTTP -> HTTPS redirect are active for DuckDNS domain
- `TELEGRAM_BOT_TOKEN` is present on VPS backend env (prior blocker fixed)
- Role switch UI, detail back button, status cards, price field polish, and no-zoom form sizing are already deployed
- Legal pages/links are now committed, pushed, and deployed
- Lifecycle status expansion (`matched/completed/cancelled`) is now committed, pushed, migrated, and deployed
- Production API behavior for lifecycle states is validated (`open -> matched -> completed/cancelled`)

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
| 1.10 | Integration manual Telegram/browser verification | IN_PROGRESS | still pending full real-device pass |
| 1.11 | Legal pages + footer/access legal links | DONE | shipped in `f13f6b6` |
| 1.12 | Legal-only commit + VPS deploy | DONE | live on `https://iindiinda.duckdns.org` |
| 1.13 | Lifecycle status expansion for targets/responses | DONE | shipped in `a3591a0` |
| 1.14 | QA complete E2E verification | IN_PROGRESS | pending full manual Telegram/browser UI pass |

## Active Blockers

- Full manual Telegram Mini App UI verification is still pending on real devices
- VPS DB migration flow was recovered after a historical partial apply; monitor schema health in next deploy window
- No local `php`, `composer`, or `docker` in this environment for full backend execution

## Decisions Already Taken

- Use `useLazyAsyncData` for AYAN pages
- Keep app dark-only with Nuxt UI primary/neutral colors
- Keep browser flow TMA-first for production auth behavior
- Ship legal pack first as isolated deploy, then continue lifecycle feature work

## Next Practical Step

1. Run full manual Telegram/browser UI E2E for create/respond/accept/match-complete/match-cancel flows
2. Validate lifecycle statuses in live AYAN UI on `/ayan`, `/ayan/trip/:id`, `/ayan/request/:id`, and `My` tab cards
3. Compare manual UI outcomes with already green API smoke behavior
4. Record any runtime edge cases from production and patch in a focused follow-up slice

## Definition Of Progress For This Sprint

This sprint is complete only when:

- AYAN flows work against real backend on live HTTPS deployment
- legal pages and links are committed and live in production
- lifecycle status expansion is verified and deployed as a separate safe slice
- `vault/` contains an exact stop point with no hidden chat-only state
