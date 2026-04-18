# Laravel API Endpoints — Ayan

## Base: /api

All routes inside `auth:sanctum` middleware (except auth endpoints).

## Auth

| Method | Path | Description | Body | Response |
|--------|------|-------------|------|----------|
| POST | /auth/telegram | Login via Telegram | `{init_data}` | `AuthResponse` |

## User

| Method | Path | Description | Body | Response |
|--------|------|-------------|------|----------|
| GET | /user | Current user | — | `User` |
| POST | /user/switch-role | Switch role | `{role}` | `User` |
| GET | /user/availability | Driver availability | — | `{is_available}` |
| POST | /user/availability | Set availability | `{is_available}` | `User` |

## Ayan Orders

| Method | Path | Description | Initiator | Body | Response |
|--------|------|-------------|-----------|------|----------|
| POST | /ayan/orders | Create order | Passenger | `CreateTaxiOrderRequest` | `TaxiOrder` |
| GET | /ayan/orders/open | Open orders | Driver | — (query: page) | `PaginatedResponse<TaxiOrder>` |
| GET | /ayan/orders/my | My active order | Both | — | `TaxiOrder \| null` |
| POST | /ayan/orders/{id}/accept | Accept order | Driver | — | `TaxiOrder` |
| POST | /ayan/orders/{id}/arrive | Mark arrived | Driver | — | `TaxiOrder` |
| POST | /ayan/orders/{id}/start | Start trip | Driver | — | `TaxiOrder` |
| POST | /ayan/orders/{id}/complete | Complete trip | Driver | `{rating?}` | `TaxiOrder` |
| POST | /ayan/orders/{id}/cancel | Cancel order | Passenger | `{reason?}` | `TaxiOrder` |

## Status State Machine

```
         ┌─── accept ───→ accepted ─── arrive ───→ arrived ─── start ───→ in_progress ─── complete ───→ completed
         │                  │                      │                       │
open ────┤                  │                      │                       │
         │                  └── cancel ────────────┴───────────────────────┘
         │                                                                     │
         └── cancel ───────────────────────────────────────────────────────────┘
                                                                                ▼
                                                                           cancelled
```

## Validation Rules

### Create Order
| Field | Rule |
|-------|------|
| from_address | required, string, max:255 |
| to_address | required, string, max:255 |
| price | required, integer, min:100, max:5000 |
| comment | nullable, string, max:500 |

### Switch Role
| Field | Rule |
|-------|------|
| role | required, in:passenger,driver |

### Set Availability
| Field | Rule |
|-------|------|
| is_available | required, boolean |

### Complete Trip
| Field | Rule |
|-------|------|
| rating | nullable, integer, min:1, max:5 |

### Cancel Order
| Field | Rule |
|-------|------|
| reason | nullable, string, max:500 |

## Backend Implementation Order

1. Migrations: users, orders tables
2. Models: User, TaxiOrder
3. AuthController (telegram login)
4. UserController (me, switch-role, availability)
5. OrderController (CRUD + status transitions)
6. Add arrive/start endpoints to api.php
7. Fix endpoint paths (/user not /user/me)

See also: [[wiki/architecture/api-contract]], [[wiki/architecture/data-models]]
