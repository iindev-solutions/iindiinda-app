# TAL service layer

TAL is the lightweight booking surface inside iind.app.

## Product shape

First real MVP slice focuses on:

1. master publishes an availability card
2. client browses the feed
3. client sends a booking request
4. master accepts or rejects
5. Telegram contact is revealed after acceptance

This slice does **not** implement:

- medical or clinic flows
- automatic slot reservation
- calendar/time-slot engine
- ratings/reviews
- public fallback requests yet

## Current source routes

- `/tal` — TAL feed, filters, role helper, create CTA, my-area tabs
- `/tal/master/[id]` — TAL master detail, booking form, owner-side bookings, final actions

## Frontend files

- `app/pages/tal.vue` — wrapper-only parent route
- `app/pages/tal/index.vue` — live TAL entry/feed page
- `app/pages/tal/master/[id].vue` — TAL detail page
- shared `~/app/components/AppAccessState.vue` — Telegram/auth access states used directly in TAL pages
- `app/components/TalRoleSwitch.vue` — TAL helper to enter/leave master mode
- `app/components/TalCreateSlideover.vue` — create availability card form
- `app/types/tal.ts` — TAL DTOs and enums
- `app/composables/useTalMasters.ts` — feed/detail/create/update API layer
- `app/composables/useTalBookings.ts` — booking list/create/update/delete API layer
- `app/composables/useTalMy.ts` — my TAL cards and bookings API layer

## Backend/API

Canonical contract:

- `vault/wiki/services/tal/api-contract.md`

First persisted slice uses:

- `GET/POST /api/tal/masters`
- `GET/PATCH /api/tal/masters/{id}`
- `GET/POST /api/tal/masters/{id}/bookings`
- `PATCH/DELETE /api/tal/bookings/{id}`
- `GET /api/tal/my/masters`
- `GET /api/tal/my/bookings`

## Deployment status

- current deployment status: the first real TAL availability + booking MVP slice is now deployed live
- live runtime now includes `/tal` feed tabs, `/tal/master/[id]`, create availability, booking requests, accept/reject, contact reveal, and final outcome actions
