# Data Models — Ayan

## TaxiOrderStatus (enum)

```typescript
type TaxiOrderStatus =
  | 'open'
  | 'accepted'
  | 'arrived'
  | 'in_progress'
  | 'completed'
  | 'cancelled'
```

## User

```typescript
interface User {
  id: number
  telegram_id: number
  first_name: string
  username: string | null
  photo_url: string | null
  role: 'passenger' | 'driver'
  phone: string | null
  is_available: boolean  // только для driver role
  created_at: string
  updated_at: string
}
```

## TaxiOrder

```typescript
interface TaxiOrder {
  id: number
  passenger_id: number
  driver_id: number | null
  from_address: string
  to_address: string
  price: number
  comment: string | null
  status: TaxiOrderStatus
  cancel_reason: string | null
  rating: number | null
  passenger: User
  driver: User | null
  created_at: string
  updated_at: string
  accepted_at: string | null
  arrived_at: string | null
  started_at: string | null
  completed_at: string | null
  cancelled_at: string | null
}
```

## CreateTaxiOrderRequest

```typescript
interface CreateTaxiOrderRequest {
  from_address: string
  to_address: string
  price: number
  comment?: string
}
```

## PaginatedResponse<T>

```typescript
interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}
```

## AuthResponse

```typescript
interface AuthResponse {
  token: string
  user: User
}
```

## ApiError

```typescript
interface ApiError {
  message: string
  code: string
  status: number
}
```

## Role Transitions

```
passenger ←→ driver  (via POST /user/switch-role)
```

При переключении на driver:
- Создаётся/активируется driver profile
- Доступны endpoint'ы: orders/open, orders/{id}/accept, arrive, start, complete

При переключении на passenger:
- Доступны: POST /orders, GET /orders/my, cancel

## Status Timelines

Каждый статус имеет timestamp:

| Поле | Когда заполняется |
|------|-------------------|
| created_at | При создании (status: open) |
| accepted_at | При accept (status: accepted) |
| arrived_at | При arrive (status: arrived) |
| started_at | При start (status: in_progress) |
| completed_at | При complete (status: completed) |
| cancelled_at | При cancel (status: cancelled) |

Эти поля позволяют UI показывать длительность каждого этапа.
