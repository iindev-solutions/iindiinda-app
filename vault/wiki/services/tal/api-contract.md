# TAL API Contract

> Status: first real MVP slice in source.
> Scope of this contract: master availability cards + client bookings + accept/reject + contact reveal.
> Deferred: public client fallback requests when no suitable master is available.

## Product Posture

TAL is a lightweight availability surface for non-medical masters.

Platform role in MVP:

- shows who is available now / later / tomorrow / busy
- lets a client send a booking request
- reveals Telegram contact only after acceptance
- does not reserve a slot automatically
- does not run a CRM or medical calendar

## Roles

- `master` can publish and manage own availability cards
- any authenticated user can browse TAL and send a booking request to another master

## Entities

### TalMaster

Represents one published availability card from a master.

```ts
TalMaster {
  id: number
  master: {
    id: number
    name: string
    username: string | null
  }
  category: 'beauty' | 'home' | 'repair'
  service_label: string
  description: string
  location: string
  availability_status: 'now' | 'later' | 'tomorrow' | 'busy'
  available_note: string | null
  price_from: number | null
  status: 'open' | 'matched' | 'completed' | 'cancelled'
  created_at: string
}
```

### TalBooking

Represents a client booking request to a master card.

```ts
TalBooking {
  id: number
  tal_master_id: number
  tal_master: TalMaster | null
  user: {
    id: number
    name: string
    username: string | null
  }
  message: string | null
  desired_time: string | null
  status: 'pending' | 'accepted' | 'rejected'
  created_at: string
}
```

## Endpoints

All TAL endpoints are authenticated under `/api/tal/*`.

### Feed + Master Cards

#### `GET /api/tal/masters`

Return public master availability feed.

Query params:

- `category?: beauty|home|repair`
- `availability_status?: now|later|tomorrow|busy`
- `location?: string`

Behavior:

- returns cards ordered newest first
- intended feed scope is active cards (`status = open`)

#### `POST /api/tal/masters`

Create a master availability card.

Allowed only for users with role `master`.

Request body:

```json
{
  "category": "beauty",
  "service_label": "Haircut",
  "description": "Short haircut or fade. Today only.",
  "location": "Center",
  "availability_status": "later",
  "available_note": "After 18:00",
  "price_from": 1500
}
```

Validation:

- `category` required
- `service_label` required, max `120`
- `description` required, max `500`
- `location` required, max `255`
- `availability_status` required
- `available_note` optional, max `255`
- `price_from` optional integer `>= 0`

#### `GET /api/tal/masters/{id}`

Return one TAL master card.

#### `PATCH /api/tal/masters/{id}`

Update own TAL master card.

Allowed only for the owner.

Updatable fields:

- any create field
- `status`: `open | completed | cancelled`

Rules:

- `matched` is system-set when a booking is accepted
- `open -> completed` is not allowed without a prior match
- final states stay final

### Bookings

#### `GET /api/tal/masters/{id}/bookings`

Return bookings for one master card.

Allowed only for that card owner.

#### `POST /api/tal/masters/{id}/bookings`

Create booking request to a master card.

Request body:

```json
{
  "message": "Need a haircut today",
  "desired_time": "After 19:00"
}
```

Validation:

- at least one of `message` or `desired_time` must be present
- max `500` / `255`

Rules:

- cannot book own card
- cannot book closed card
- cannot book card with `availability_status = busy`
- one booking per user per card

#### `PATCH /api/tal/bookings/{id}`

Update booking status.

Allowed only for card owner.

Request body:

```json
{
  "status": "accepted" | "rejected"
}
```

Rules:

- only pending bookings can change
- accepting one booking sets card status to `matched`
- accepting one booking auto-rejects other pending bookings for the same card

#### `DELETE /api/tal/bookings/{id}`

Delete own pending booking.

Allowed only for booking author.

## My Endpoints

### `GET /api/tal/my/masters`

Return cards created by the authenticated master.

### `GET /api/tal/my/bookings`

Return bookings created by the authenticated user.

Response includes linked `tal_master` card when available.

## UI Expectations

Frontend MVP should provide:

1. `/tal` feed with tabs:
   - feed
   - my statuses
   - my bookings
2. filters for category / availability / location
3. master role switch helper on TAL page
4. create slideover for availability cards
5. `/tal/master/[id]` detail page with:
   - booking form
   - owner-side booking list
   - accept/reject actions
   - contact reveal after acceptance
   - final outcome actions (`completed` / `cancelled`)

## Deferred After This Slice

Not part of this first real slice:

- public client fallback requests
- master favorites
- ratings/reviews
- automatic slot reservation
- calendar/time-slot engine
- medical/clinic scenarios
