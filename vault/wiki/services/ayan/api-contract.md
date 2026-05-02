# AYAN API Contract

> Part of: [[wiki/architecture/roadmap]]
> Vision: [[wiki/architecture/ayan-vision]]

## Base URL

```
/api/ayan
```

## Authentication

All protected endpoints require `Authorization: Bearer {token}` header.

- Login entrypoint: `POST /api/auth/telegram`
- Backend currently issues Sanctum personal access token, not JWT
- Production login must use signed Telegram `initData`
- Local/testing may use `init_data = test`

---

## Endpoints

### Trips (Поездки — от водителя)

#### GET /ayan/trips
List all open trips (with optional filters).

**Query params:**
- `from` (optional) — filter by departure city
- `to` (optional) — filter by destination city
- `date` (optional) — filter by date (YYYY-MM-DD)

**Response:**
```json
{
  {
    success: true,
    data: [
      {
        id: 1,
        driver: { id: 1, name: string, username: string },
        from_address: string,
        to_address: string,
        date: string,
        time: string,
        seats: number,
        price: number,
        comment: string | null,
        status: 'open',
        created_at: string
      }
    ]
  }
}
```

---

#### POST /ayan/trips
Create a new trip (driver only).

**Body:**
```json
{
  from_address: string,
  to_address: string,
  date: string,
  time: string,
  seats: number,
  price: number,
  comment: string | null
}
```

**Validation:**
- `from_address`: required, string, max 255
- `to_address`: required, string, max 255
- `date`: required, date, must be today or future
- `time`: required, string (HH:MM)
- `seats`: required, integer, min 1, max 10
- `price`: required, integer, min 0
- `comment`: optional, string, max 500

**Response:**
```json
{
  success: true,
  data: { /* Trip object */ }
}
```

---

#### GET /ayan/trips/{id}
Get single trip details.

**Response:**
```json
{
  success: true,
  data: {
    id: number,
    driver: { id, name, username },
    from_address: string,
    to_address: string,
    date: string,
    time: string,
    seats: number,
    price: number,
    comment: string | null,
    status: 'open' | 'closed',
    created_at: string
  }
}
```

---

#### PATCH /ayan/trips/{id}
Update trip (driver only, owner only).

**Body:**
```json
{
  seats: number,
  price: number,
  comment: string,
  status: 'open' | 'closed'
}
```

---

### Requests (Запросы — от пассажира)

#### GET /ayan/requests
List all open requests (with optional filters).

**Query params:**
- `from` (optional)
- `to` (optional)
- `date` (optional)

**Response:**
```json
{
  success: true,
  data: [
    {
      id: 1,
      passenger: { id: 1, name: string, username: string },
      from_address: string,
      to_address: string,
      date: string,
      time: string | null,
      description: string | null,
      status: 'open',
      created_at: string
    }
  ]
}
```

---

#### GET /ayan/requests/{id}
Get single request details.

**Response:**
```json
{
  success: true,
  data: {
    id: number,
    passenger: { id, name, username },
    from_address: string,
    to_address: string,
    date: string,
    time: string | null,
    description: string | null,
    status: 'open' | 'closed',
    created_at: string
  }
}
```

---

#### POST /ayan/requests
Create a new request (passenger only).

**Body:**
```json
{
  from_address: string,
  to_address: string,
  date: string,
  time: string | null,
  description: string | null
}
```

**Validation:**
- `from_address`: required
- `to_address`: required
- `date`: required, today or future
- `time`: optional
- `description`: optional, max 500

---

#### PATCH /ayan/requests/{id}
Update request (owner only).

**Body:**
```json
{
  status: 'open' | 'closed'
}
```

---

### Responses (Отклики)

#### GET /ayan/trips/{id}/responses
Get responses for a trip (owner only).

**Response:**
```json
{
  success: true,
  data: [
    {
      id: number,
      trip_id: number | null,
      request_id: number | null,
      user: { id, name, username },
      message: string | null,
      status: 'pending' | 'accepted' | 'rejected',
      created_at: string
    }
  ]
}
```

---

#### GET /ayan/requests/{id}/responses
Get responses for a request (owner only).

---

#### POST /ayan/trips/{trip_id}/responses
Respond to a trip (passenger).

**Body:**
```json
{
  message: string | null
}
```

---

#### POST /ayan/requests/{request_id}/responses
Respond to a request (driver).

**Body:**
```json
{
  message: string | null
}
```

---

#### PATCH /ayan/responses/{id}
Update response status (owner only).

**Body:**
```json
{
  status: 'accepted' | 'rejected'
}
```

**Rules:**
- only trip owner / request owner may update
- only pending response may be updated
- accepting closes parent trip/request
- accepting auto-rejects other pending responses for same target

---

#### DELETE /ayan/responses/{id}
Cancel response (owner only).

---

### User Trips (My Trips)

#### GET /ayan/my/trips
Get current user's trips (as driver).

**Response:** Array of Trip objects

---

#### GET /ayan/my/requests
Get current user's requests (as passenger).

**Response:** Array of Request objects

---

#### GET /ayan/my/responses
Get current user's responses.

**Response:** Array of Response objects

---

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK |
| 201 | Created |
| 400 | Validation error |
| 401 | Unauthorized |
| 403 | Forbidden (not owner) |
| 404 | Not found |
| 422 | Unprocessable entity |

---

## Error Response Format

```json
{
  success: false,
  message: string,
  errors: {
    field_name: [string]
  }
}
```

---

## Implementation Order

1. POST /ayan/trips
2. GET /ayan/trips
3. POST /ayan/requests
4. GET /ayan/requests
5. POST /ayan/trips/{id}/responses
6. POST /ayan/requests/{id}/responses
7. GET /ayan/trips/{id}/responses
8. GET /ayan/my/* (my trips, requests, responses)
9. PATCH /ayan/trips/{id}
10. PATCH /ayan/requests/{id}

---

## Data Model

```sql
users
  id          bigint PK
  tg_id       bigint UNIQUE
  first_name  varchar(255)
  username    varchar(255) NULL
  created_at  timestamp

trips
  id           bigint PK
  driver_id    bigint FK -> users.id
  from_address varchar(255)
  to_address   varchar(255)
  date         date
  time         varchar(5)
  seats        tinyint
  price        int
  comment      text NULL
  status       enum('open', 'closed')
  created_at   timestamp

requests
  id           bigint PK
  passenger_id bigint FK -> users.id
  from_address varchar(255)
  to_address   varchar(255)
  date         date
  time         varchar(5) NULL
  description  text NULL
  status       enum('open', 'closed')
  created_at   timestamp

responses
  id           bigint PK
  user_id      bigint FK -> users.id
  trip_id      bigint FK -> trips.id NULL
  request_id   bigint FK -> requests.id NULL
  message      text NULL
  created_at   timestamp
```

---

## NOTES

- Trip OR Request ID in response must be set, not both
- All timestamps in ISO 8601 format
- Driver = creator of trip
- Passenger = creator of request
