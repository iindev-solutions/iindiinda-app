# Sprint — Phase 1 AYAN MVP

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

## Resume Point — 2026-04-23

- Current branch: `front/ayan`
- Latest important hardening commit already pushed: `755f7c6` `fix(ayan): enforce auth and response rules`
- Main blocker: HTTPS is still missing for the VPS-served frontend because there is no hostname/domain attached to the server yet
- Last completed action: added frontend AYAN role-switch UI, deployed SPA static build to VPS root over HTTP, and kept `/api` served by the backend on the same VPS
- Continue from: `vault/resume-plan.md`

## Current Reality

- Frontend AYAN is wired to the real API
- Backend AYAN runtime is already live on VPS with Laravel + Sanctum + MySQL persistence
- GitHub Pages frontend is live at `https://iindev-solutions.github.io/iindiinda-app/`
- The VPS root `http://89.22.226.34/` now serves the frontend SPA over Nginx static files
- The VPS route `http://89.22.226.34/api/*` still serves the Laravel backend
- Direct AYAN API smoke against VPS was previously green for auth, create, respond, accept, and `my/*`
- SSH access from this machine to `iind-vps` is working again
- The VPS backend runtime is now synchronized to the branch tip and passes focused feature tests on the clean deployed checkout
- A local hardening slice is already pushed and includes:
  - signed Telegram `initData` parsing
  - scoped `init_data=test` fallback for local/testing only
  - role and owner enforcement for AYAN actions
  - frontend alignment so non-owners do not call owner-only response endpoints
- Current product role model:
- new login defaults to `passenger`
- `driver`/`passenger` separation is enforced in backend and frontend guards
- switching role is now exposed in AYAN UI and backed by the existing `/api/user/switch-role` endpoint

## Sprint Tasks

| # | Task | Status | Blockers |
|---|------|--------|----------|
| 1.1 | Backend migrations (`users`, `trips`, `requests`, `responses`) | DONE | — |
| 1.2 | Backend AYAN models and controllers | DONE* | production runtime verification for the new hardening slice is still pending |
| 1.3 | Frontend AYAN structure (`pages`, `composables`, `types`) | DONE | — |
| 1.4 | Frontend create flow (`slideover`, form switching) | DONE | — |
| 1.5 | Frontend feed, tabs, filters, empty states | DONE | — |
| 1.6 | Frontend performance pass (`lazy`, `useLazyAsyncData`) | DONE | — |
| 1.7 | Response flow, contact link, accept/reject | DONE | — |
| 1.8 | Feed filters by route/date | DONE | — |
| 1.9 | Nuxt UI color alignment | DONE | — |
| 1.10 | Integration: mock -> real API | IN_PROGRESS | browser auth limitations, HTTPS/domain still missing on VPS frontend |
| 1.11 | QA: complete E2E verification | TODO | 1.10 |

`DONE*` means the slice is implemented and partially verified, but one important verification layer is still pending.

## Active Blockers

- No local `php`, `composer`, or `docker` in this environment
- Browser auth remains intentionally limited until the Telegram auth flow is fully finalized end-to-end
- VPS frontend currently works over plain HTTP only; trusted HTTPS still needs a hostname/domain strategy

## Decisions Already Taken

- Use `useLazyAsyncData` for AYAN pages
- Keep the app dark-only with Nuxt UI primary/neutral colors
- Use a single AYAN create slideover instead of separate create pages
- Keep browser auth in TMA-first mode until the production Telegram path is fully verified

## Next Practical Step

1. Manually test AYAN against `http://89.22.226.34/` with the new role switcher
2. Decide hostname strategy for HTTPS:
   - buy/attach a real domain, or
   - use a free hostname service you control
3. After hostname exists, issue a real TLS certificate and switch VPS frontend to HTTPS
4. Re-test Telegram/TMA constraints after HTTPS is available
5. Update `vault/` again with the exact result

## Definition Of Progress For This Sprint

This sprint is complete only when:

- AYAN flows work against the real backend
- the backend auth/authorization hardening is verified on a clean synchronized VPS checkout
- driver/passenger role selection is reachable in the real user flow
- the current stop point is documented in `vault/`
