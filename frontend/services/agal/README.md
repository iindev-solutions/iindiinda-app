# iind.agal — Аҕал (Delivery via existing routes)

**Version**: v0.3
**Status**: backend persistence shipped, frontend MVP flow in progress

## Core concept

AGAL connects two sides:

1. a **carrier** who is already traveling on a route
2. a **sender** who needs to send an item in the same direction

The platform does **not** deliver the parcel itself.

AGAL is not:

- a logistics company
- a courier service
- a warehouse
- an insurer
- a tracking system

It only helps people find each other and agree directly.

## MVP pattern

AGAL intentionally reuses the AYAN interaction model:

1. carrier creates a route
2. sender creates a request
3. both sides browse feed
4. one side responds
5. owner accepts one response
6. contact is revealed
7. owner marks outcome as `completed` or `cancelled`

## API shape

Base URL:

```text
/api/agal
```

Main surfaces:

```text
GET    /agal/routes
POST   /agal/routes
GET    /agal/routes/{id}
PATCH  /agal/routes/{id}

GET    /agal/requests
POST   /agal/requests
GET    /agal/requests/{id}
PATCH  /agal/requests/{id}

GET    /agal/routes/{id}/responses
POST   /agal/routes/{id}/responses
GET    /agal/requests/{id}/responses
POST   /agal/requests/{id}/responses
PATCH  /agal/responses/{id}
DELETE /agal/responses/{id}

GET    /agal/my/routes
GET    /agal/my/requests
GET    /agal/my/responses
```

## Current implementation note

Current repository state now includes:

- real backend persistence for `routes / requests / responses / my/*`
- AGAL frontend feed, create flow, role switcher, and detail pages mirroring AYAN MVP patterns
- live contact-reveal and lifecycle UI for `open / matched / completed / cancelled`

Still not finished:

- broader UX polish and deeper AGAL-specific refinements beyond the narrow MVP slice

Source of truth:

- `vault/wiki/services/agal/api-contract.md`
