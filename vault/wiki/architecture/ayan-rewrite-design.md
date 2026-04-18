# Ayan Rewrite Design

## Decision Log

| Вопрос | Решение | Обоснование |
|--------|---------|-------------|
| Масштаб | Ayan + базовые composables | Остальные сервисы — заглушки |
| Статус-флоу | 6 статусов (open → accepted → arrived → in_progress → completed + cancelled) | Водитель контролирует поездку |
| Mock API | Интерфейс ITaxiAPI + 2 реализации | Типобезопасность, без хаков |
| Auth | Авто-логин через Telegram + localhost mock | Бесшовный UX |
| Real-time | Поллинг с умным бэкоффом | Просто, достаточно для MVP |
| Роли | Выбор при входе (MVP) | Быстрый старт |
| Подход | Гибридный: фундамент → пассажир → водитель → шеринг | Быстрый результат + чистая архитектура |

## Phase 0: Foundation

### 0.1 Types (frontend/app/types/)
- Переписать `api.ts` — убрать double-optional, добавить timestamp поля
- Добавить `ITaxiAPI` интерфейс со всеми методами
- Убрать неиспользуемые типы (tal, uus, agal) — вернуть когда понадобятся

### 0.2 Interface ITaxiAPI

```typescript
interface ITaxiAPI {
  // Orders
  createOrder(data: CreateTaxiOrderRequest): Promise<TaxiOrder>
  getOpenOrders(page?: number): Promise<PaginatedResponse<TaxiOrder>>
  getMyOrder(): Promise<TaxiOrder | null>
  getOrder(id: number): Promise<TaxiOrder>

  // Driver actions
  acceptOrder(id: number): Promise<TaxiOrder>
  markArrived(id: number): Promise<TaxiOrder>
  startTrip(id: number): Promise<TaxiOrder>
  completeTrip(id: number, rating?: number): Promise<TaxiOrder>

  // Passenger actions
  cancelOrder(id: number, reason?: string): Promise<TaxiOrder>

  // User
  getUser(): Promise<User>
  switchRole(role: 'passenger' | 'driver'): Promise<User>
  setAvailability(isAvailable: boolean): Promise<User>

  // Auth
  login(initData: string): Promise<AuthResponse>
}
```

### 0.3 TaxiApiClient (real API)
- Использует `$fetch` под капотом
- URL: `runtimeConfig.public.apiBase`
- Auth: Bearer token из `useAuth`
- Error handling: трансформация `$fetch` ошибок → `ApiError`

### 0.4 TaxiApiMock (mock)
- In-memory state с localStorage persistence
- Настраиваемые задержки и error rate
- Полная реализация статус-машины (6 статусов)
- Тот же возвращаемый тип, что и TaxiApiClient
- Авто-матчинг заказов (настраиваемая задержка)

### 0.5 useAuth rewrite
- autoLogin() — из middleware
- Telegram mode: initData → POST /auth/telegram
- Localhost mode: mock user auto-creation
- Token persistence: useState + localStorage
- Роль сохраняется в user object

### 0.6 useTg fixes
- Вызывать ready() после монтирования
- Вызывать expand() для полноэкранного режима
- BackButton: cleanup при unmount (offClick)
- MainButton: cleanup onClick при hide

### 0.7 usePolling (new composable)
```typescript
function usePolling<T>(
  fetcher: () => Promise<T>,
  options: {
    interval: number          // base interval в ms
    maxInterval?: number      // max при backoff
    backoffMultiplier?: number // default 1.5
    enabled?: Ref<boolean>     // reactive toggle
    onError?: (error: any) => void
    onSuccess?: (data: T) => void
  }
): {
  data: Ref<T | null>
  error: Ref<any>
  isPolling: Ref<boolean>
  pause: () => void
  resume: () => void
  refresh: () => Promise<void>
}
```
- Экспоненциальный бэкофф при ошибках
- Авто-pause при уходе со страницы (visibility API)
- Reset interval при успехе после ошибки

## Phase 1: Passenger Flow

### Страницы
1. `/ayan` — хаб: выбор роли (пассажир/водитель) + кнопка "Мой заказ"
2. `/ayan/create` — форма создания заказа
3. `/ayan/my-order` — трекинг активного заказа
4. `/ayan/complete` — завершение + рейтинг

### Пассажирский флоу
```
/index → /ayan → [выбрать "Пассажир"] → /ayan/create
  → (создал заказ) → /ayan/my-order
    → (статус open) → анимация поиска
    → (статус accepted) → инфо водителя
    → (статус arrived) → "Водитель на месте"
    → (статус in_progress) → "Поездка"
    → (статус completed) → /ayan/complete
  → (отменил) → /ayan
```

### my-order.vue redesign
- Поллинг через `usePolling` (5 сек, бэкофф)
- Статус-карта: одна конфигурация, разные секции по роли
- Cancel → confirmation modal → POST /cancel
- Переход на complete через navigateTo с query orderId

### complete.vue redesign
- Получает orderId из query params
- Фетчит заказ для показа реальных данных
- Рейтинг отправляется через `completeTrip(id, rating)`
- Кнопка "Новая поездка" → /ayan/create
- Кнопка "На главную" → /

## Phase 2: Driver Flow

### Страницы
5. `/ayan/driver` — дашборд водителя
6. `/ayan/orders` — список открытых заказов
7. `/ayan/active-ride` — текущий заказ водителя

### Водительский флоу
```
/index → /ayan → [выбрать "Водитель"] → /ayan/driver
  → [Включил онлайн] → /ayan/orders
    → (принял заказ) → /ayan/active-ride
      → (статус accepted) → кнопка "Я на месте"
      → (статус arrived) → кнопка "Начать поездку"
      → (статус in_progress) → кнопка "Завершить"
      → (статус completed) → /ayan/complete
    → (пассажир отменил) → /ayan/orders
```

### driver.vue redesign
- Переключатель online/offline
- USwitch: optimistic OFF, confirm ON after API
- Статистика: completed orders count

### orders.vue redesign
- Поллинг через `usePolling` (10 сек, бэкофф)
- Карточка заказа — общий компонент OrderCard
- Пагинация через PaginatedResponse

### active-ride.vue redesign
- Поллинг через `usePolling` (5 сек, бэкофф)
- Кнопки действий зависят от текущего статуса
- Если заказ отменён пассажиром → toast + переход на /ayan/orders

## Phase 3: Shared Components & Utilities

### Извлечь из страниц
1. `OrderCard.vue` — карточка заказа (used in orders.vue, my-order.vue, active-ride.vue)
2. `StatusBadge.vue` — бейдж статуса с цветом
3. `DriverInfo.vue` — блок с информацией о водителе
4. `PriceDisplay.vue` — форматированная цена
5. `RatingStars.vue` — виджет рейтинга с обработчиком

### Извлечить из логики
1. `statusConfig` → общий map в composables/useOrderStatus.ts
2. `formatPrice()` → utils/format.ts
3. `cancelConfirm` → общий confirm modal pattern

## File Structure (after rewrite)

```
frontend/app/
├── types/
│   └── api.ts                          # All types + ITaxiAPI interface
├── composables/
│   ├── useAPI.ts                        # Base HTTP client (slim)
│   ├── useAuth.ts                       # Auth + auto-login + persistence
│   ├── useTg.ts                         # Telegram SDK (fixed)
│   ├── usePolling.ts                    # Smart polling composable
│   ├── useTaxiAPI.ts                    # Factory: returns ITaxiAPI impl
│   ├── useOrderStatus.ts               # Status config map + helpers
│   └── useTaxiOrder.ts                 # Order state management
├── utils/
│   └── format.ts                        # formatPrice, formatDate, etc.
├── middleware/
│   └── auth.global.ts                   # Auto-login middleware
├── config/
│   └── api.config.ts                    # Mock toggle (runtime)

frontend/services/ayan/app/
├── pages/
│   ├── ayan.vue                         # Parent wrapper (<NuxtPage />)
│   └── ayan/
│       ├── index.vue                    # Hub: role selection + CTAs
│       ├── create.vue                   # Create order form
│       ├── my-order.vue                 # Passenger: track active order
│       ├── driver.vue                   # Driver: dashboard
│       ├── orders.vue                   # Driver: available orders
│       ├── active-ride.vue             # Driver: active ride management
│       └── complete.vue                 # Both: ride complete + rating
├── components/
│   ├── OrderCard.vue                    # Order display card
│   ├── StatusBadge.vue                  # Status indicator
│   ├── DriverInfo.vue                   # Driver info block
│   ├── PriceDisplay.vue                 # Formatted price
│   └── RatingStars.vue                  # Star rating widget
```

## Mock/Real Toggle (Runtime)

```typescript
// nuxt.config.ts
runtimeConfig: {
  public: {
    apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
    useMockApi: process.env.NUXT_PUBLIC_USE_MOCK_API === 'true'
  }
}

// useTaxiAPI.ts
export function useTaxiAPI(): ITaxiAPI {
  const config = useRuntimeConfig()
  return config.public.useMockApi ? new TaxiApiMock() : new TaxiApiClient()
}
```

Нет перезапуска dev server при переключении — просто меняешь `.env`.

## i18n Structure (fix)

Текущая проблема: смешанные flat/nested ключи.

Новая структура:
```json
{
  "ayan": {
    "title": "Бардыбыт",
    "category": "Такси",
    "badge": "MVP",
    "createSubtitle": "Создать новый заказ",
    "steps": {
      "1": "Укажите маршрут и цену",
      "2": "Ожидайте водителя",
      "3": "Отправляйтесь в путь"
    },
    "order": {
      "from": "Откуда",
      "to": "Куда",
      "price": "Цена",
      "comment": "Комментарий",
      "status": {
        "open": { "title": "Ищем водителя", "description": "Ожидайте..." },
        "accepted": { "title": "Водитель принят", "description": "Водитель в пути" },
        "arrived": { "title": "Водитель на месте", "description": "Выходите" },
        "in_progress": { "title": "Поездка", "description": "В пути" },
        "completed": { "title": "Завершено", "description": "Поездка окончена" },
        "cancelled": { "title": "Отменено", "description": "Заказ отменён" }
      }
    },
    "actions": {
      "create": "Создать заказ",
      "accept": "Принять",
      "arrive": "Я на месте",
      "start": "Начать поездку",
      "complete": "Завершить",
      "cancel": "Отменить"
    },
    "validation": {
      "priceMin": "Минимальная цена 100 ₽",
      "priceMax": "Максимальная цена 5000 ₽",
      "addressRequired": "Укажите адрес"
    }
  }
}
```

## Backend Changes Required

### New Endpoints (not in current api.php)
1. `POST /api/ayan/orders/{id}/arrive` — driver marks arrived
2. `POST /api/ayan/orders/{id}/start` — driver starts trip
3. `POST /api/user/availability` — set driver online/offline
4. `GET /api/user/availability` — get driver availability

### Endpoint Fixes
1. `GET /api/user` (not `/user/me`)
2. `GET /api/ayan/orders/open` (not `/ayan/orders`)
3. `GET /api/ayan/orders/my` (not `/ayan/orders/active`)

### New Fields on TaxiOrder
1. `accepted_at: timestamp`
2. `arrived_at: timestamp`
3. `started_at: timestamp`
4. `completed_at: timestamp`
5. `cancelled_at: timestamp`
6. `rating: number | null`
7. `cancel_reason: string | null`

## Roadmap

```
Phase 0: Foundation ──────────────────────── ~2-3 дня
  ├─ types/api.ts (ITaxiAPI + all models)
  ├─ TaxiApiClient + TaxiApiMock
  ├─ useAuth rewrite
  ├─ useTg fixes
  ├─ usePolling composable
  └─ auth.global.ts middleware

Phase 1: Passenger Flow ──────────────────── ~2 дня
  ├─ ayan/index.vue (role selection)
  ├─ ayan/create.vue
  ├─ ayan/my-order.vue
  ├─ ayan/complete.vue
  └─ i18n fixes

Phase 2: Driver Flow ────────────────────── ~2 дня
  ├─ ayan/driver.vue
  ├─ ayan/orders.vue
  ├─ ayan/active-ride.vue
  └─ driver-specific i18n

Phase 3: Shared & Polish ─────────────────── ~1-2 дня
  ├─ Extract shared components
  ├─ Extract useOrderStatus + formatPrice
  ├─ Fix all i18n keys
  └─ Update README

Backend (parallel) ─────────────────────────
  ├─ Add arrive/start/availability endpoints
  ├─ Fix endpoint paths
  ├─ Add timestamps to orders
  └─ Implement controllers + migrations
```

## Success Criteria (MVP)

1. Пассажир может создать заказ, видеть статус, отменить
2. Водитель может видеть заказы, принять, отметить прибытие, начать, завершить
3. Рейтинг отправляется и сохраняется
4. Auth работает в Telegram и на localhost
5. Все i18n ключи работают (нет сломанных)
6. Нет утечек обработчиков (backButton, polling)
7. Токен персистится между сессиями
8. Переключение mock/real без перезапуска

## Future (post-MVP)

- WebSocket для real-time
- Геокодинг адресов + карта
- Контакты водитель↔пассажир (звонок)
- Переговоры по цене
- История поездок
- Админ-панель модерации водителей
- Push-уведомления
- Anti-abuse system (see: [[anti abuse schem for taxi]])

See also: [[wiki/architecture/system-design]], [[wiki/architecture/api-contract]], [[wiki/architecture/data-models]], [[wiki/architecture/auth-flow]]
