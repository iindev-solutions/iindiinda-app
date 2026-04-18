# API Contract — Ayan (Taxi)

## Base URL
- Production: `https://api.iind.app/api`
- Dev: `http://localhost:8000/api`
- Mock: in-memory (localStorage persistence)

## Auth
All ayan endpoints require `auth:sanctum` middleware.

### POST /api/auth/telegram
Telegram WebApp initData → JWT token.

**Request:**
```json
{
  "init_data": "query_id=...&user=..."
}
```

**Response:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "telegram_id": 123456789,
    "first_name": "Александр",
    "username": "sasha",
    "photo_url": "https://...",
    "role": "passenger",
    "phone": null,
    "created_at": "2026-04-18T12:00:00Z",
    "updated_at": "2026-04-18T12:00:00Z"
  }
}
```

## User

### GET /api/user
Текущий пользователь.

### POST /api/user/switch-role
**Body:** `{ "role": "passenger" | "driver" }`

### GET /api/user/availability
Доступность водителя (online/offline).

### POST /api/user/availability
**Body:** `{ "is_available": true | false }`

## Ayan Orders

### POST /api/ayan/orders
Создать заказ (пассажир).

**Body:**
```json
{
  "from_address": "ул. Ленина 1",
  "to_address": "пр. Ленина 55",
  "price": 500,
  "comment": "Возьми багаж"
}
```

**Response:** `TaxiOrder`

### GET /api/ayan/orders/open
Список открытых заказов (для водителей).

**Query:** `?page=1&per_page=20`

**Response:** `PaginatedResponse<TaxiOrder>`

### GET /api/ayan/orders/my
Текущий активный заказ пользователя (и для пассажира, и для водителя).

**Response:** `TaxiOrder | null`

### POST /api/ayan/orders/{id}/accept
Водитель принимает заказ.

**Response:** `TaxiOrder` (status → accepted)

### POST /api/ayan/orders/{id}/arrive
Водитель приехал на место.

**Response:** `TaxiOrder` (status → arrived)

### POST /api/ayan/orders/{id}/start
Водитель начал поездку.

**Response:** `TaxiOrder` (status → in_progress)

### POST /api/ayan/orders/{id}/complete
Водитель завершил поездку.

**Body:**
```json
{
  "rating": 5
}
```

**Response:** `TaxiOrder` (status → completed)

### POST /api/ayan/orders/{id}/cancel
Отмена заказа.

**Body:**
```json
{
  "reason": "Водитель не приехал"
}
```

**Response:** `TaxiOrder` (status → cancelled)

## Status Flow

```
open ──→ accepted ──→ arrived ──→ in_progress ──→ completed
 │          │            │             │
 └──→ cancelled ←───────┘─────────────┘
```

### Transitions & Permissions

| From → To | Initiator | Endpoint |
|-----------|-----------|----------|
| open → accepted | Driver | POST /{id}/accept |
| open → cancelled | Passenger | POST /{id}/cancel |
| accepted → arrived | Driver | POST /{id}/arrive |
| accepted → cancelled | Passenger | POST /{id}/cancel |
| arrived → in_progress | Driver | POST /{id}/start |
| arrived → cancelled | Passenger | POST /{id}/cancel |
| in_progress → completed | Driver | POST /{id}/complete |
| in_progress → cancelled | Any (emergency) | POST /{id}/cancel |

### Status Labels (i18n)

| Status | RU | Сахылыыр |
|--------|----|-----------|
| open | Ищем водителя | Көрдөһүү |
| accepted | Водитель принят | Көлөһүт ыллынна |
| arrived | Водитель на месте | Көлөһүт кэллэ |
| in_progress | Поездка | Айан |
| completed | Завершено | Бүттэ |
| cancelled | Отменено | Арыйдыллынна |

## Error Format

```json
{
  "message": "Order not found",
  "code": "ORDER_NOT_FOUND",
  "status": 404
}
```

## Rate Limits
- Create order: 5/min per user
- Accept order: 10/min per driver
- General API: 60/min per user

See also: [[wiki/services/laravel-api/endpoints]]
