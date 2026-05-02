# AGAL API Contract

> Status: draft for post-AYAN implementation
> Purpose: define the MVP surface before AGAL code work expands beyond the current placeholder route

## Product Role

AGAL is a matching board for parcel handoff through people who are already traveling.

AGAL is **not**:

- a logistics company
- a courier service
- a carrier
- a warehouse
- an insurer
- a tracking platform

AGAL only helps two sides find each other:

1. a **carrier** who is already traveling on a route
2. a **sender** who needs to send an item on a similar route

Contact is revealed only after an explicit accept action.

## MVP Scope

AGAL MVP should mirror the proven AYAN interaction model as closely as possible:

1. carrier creates a route
2. sender creates a request
3. both sides browse feed with filters
4. one side responds
5. owner accepts one response
6. contact is revealed
7. owner marks outcome as `completed` or `cancelled`

This reuse is intentional. It reduces implementation risk and keeps the platform pattern consistent:

> request/offer -> response -> agreement

## Base URL

```text
/api/agal
```

## Authentication

All AGAL endpoints are protected.

- Login entrypoint: `POST /api/auth/telegram`
- Auth transport: `Authorization: Bearer {token}`
- Backend auth runtime should follow the same Sanctum path already used by AYAN

## Core Entities

### Route

Carrier-side offer created by a traveler who is already going somewhere.

```ts
Route {
  id: number
  carrier: {
    id: number
    name: string
    username: string | null
  }
  from_address: string
  to_address: string
  date: string
  time: string | null
  size_label: 'document' | 'small' | 'medium' | 'large'
  weight_kg_max: number | null
  accepted_items: string | null
  restricted_items: string | null
  price: number | null
  notes: string | null
  status: 'open' | 'matched' | 'completed' | 'cancelled'
  created_at: string
}
```

### Request

Sender-side need created by a user who wants to send something.

```ts
Request {
  id: number
  sender: {
    id: number
    name: string
    username: string | null
  }
  from_address: string
  to_address: string
  date: string
  time: string | null
  size_label: 'document' | 'small' | 'medium' | 'large'
  weight_kg: number | null
  contents_summary: string
  fragility: 'normal' | 'fragile'
  documents_required: boolean
  budget: number | null
  notes: string | null
  status: 'open' | 'matched' | 'completed' | 'cancelled'
  created_at: string
}
```

### Response

Shared response object used for both routes and requests.

```ts
Response {
  id: number
  user: {
    id: number
    name: string
    username: string | null
  }
  message: string | null
  status: 'pending' | 'accepted' | 'rejected'
  created_at: string
}
```

## Lifecycle Rules

### Target statuses

Both `Route` and `Request` use the same lifecycle:

- `open`
- `matched`
- `completed`
- `cancelled`

### Response statuses

- `pending`
- `accepted`
- `rejected`

### Behavior

- new target starts as `open`
- accepting a response moves target to `matched`
- owner can later mark target as `completed` or `cancelled`
- once a response is accepted, contact may be shown
- non-pending responses cannot be deleted

## Endpoint Surface

AGAL should copy the AYAN endpoint shape with AGAL naming.

### Routes

#### `GET /agal/routes`

List open routes.

**Filters:**

- `from` optional
- `to` optional
- `date` optional

#### `POST /agal/routes`

Create route.

**Body:**

```json
{
  "from_address": "Yakutsk",
  "to_address": "Moscow",
  "date": "2026-05-01",
  "time": "08:00",
  "size_label": "small",
  "weight_kg_max": 2,
  "accepted_items": "documents, clothes",
  "restricted_items": "fragile, liquids",
  "price": 500,
  "notes": "Airport handoff only"
}
```

#### `GET /agal/routes/{id}`

Get single route details.

#### `PATCH /agal/routes/{id}`

Owner updates route status/details.

### Requests

#### `GET /agal/requests`

List open requests.

**Filters:**

- `from` optional
- `to` optional
- `date` optional

#### `POST /agal/requests`

Create request.

**Body:**

```json
{
  "from_address": "Yakutsk",
  "to_address": "Novosibirsk",
  "date": "2026-05-01",
  "time": null,
  "size_label": "document",
  "weight_kg": 0.2,
  "contents_summary": "A4 documents",
  "fragility": "normal",
  "documents_required": true,
  "budget": 1000,
  "notes": "Urgent"
}
```

#### `GET /agal/requests/{id}`

Get single request details.

#### `PATCH /agal/requests/{id}`

Owner updates request status/details.

### Responses

#### `GET /agal/routes/{id}/responses`

Owner-only list of responses for route.

#### `POST /agal/routes/{id}/responses`

Respond to route.

#### `GET /agal/requests/{id}/responses`

Owner-only list of responses for request.

#### `POST /agal/requests/{id}/responses`

Respond to request.

#### `PATCH /agal/responses/{id}`

Owner accepts or rejects response.

#### `DELETE /agal/responses/{id}`

Responder deletes own pending response only.

### My Area

#### `GET /agal/my/routes`
#### `GET /agal/my/requests`
#### `GET /agal/my/responses`

Same intent as AYAN `my/*` endpoints.

## Validation Baseline

### Route validation

- `from_address`: required, string, max 255
- `to_address`: required, string, max 255
- `date`: required, today or future
- `time`: nullable
- `size_label`: required enum
- `weight_kg_max`: nullable numeric, `> 0`
- `accepted_items`: nullable string, max 500
- `restricted_items`: nullable string, max 500
- `price`: nullable integer, `>= 0`
- `notes`: nullable string, max 500

### Request validation

- `from_address`: required, string, max 255
- `to_address`: required, string, max 255
- `date`: required, today or future
- `time`: nullable
- `size_label`: required enum
- `weight_kg`: nullable numeric, `> 0`
- `contents_summary`: required string, max 255
- `fragility`: required enum
- `documents_required`: required boolean
- `budget`: nullable integer, `>= 0`
- `notes`: nullable string, max 500

## Response Limit

AGAL may later apply the same response-limit rule discussed in vision docs (`3-5` responses per target), but the first implementation slice should prefer shipping the core flow first and only then adding hard limits if needed.

## Explicit Non-Goals For MVP

Do not implement yet:

- parcel tracking
- insurance
- proof-of-delivery workflow
- in-app payments
- courier verification
- dangerous-goods workflow beyond ban language and moderation
- cross-border / customs workflow
- ratings and reviews
- recurring route automation

## Implementation Decision

AGAL should **reuse AYAN architecture** wherever possible:

- same page/tab/detail pattern
- same `my/*` pattern
- same response acceptance flow
- same target lifecycle statuses
- same contact-reveal moment
- same Telegram-first runtime assumptions

That is the chosen path because it is the fastest route to a stable post-AYAN MVP.

## First Practical Slice

Before writing large AGAL code, complete these in order:

1. lock this contract
2. align placeholder backend routes to the dual-surface model (`routes`, `requests`, `responses`, `my/*`)
3. scaffold AGAL frontend structure to mirror AYAN
4. then implement the real create/feed/respond flow slice by slice
