# CODE_MAP — Инвентарь кода iindiinda-app

> ИИ: читай этот файл чтобы понимать где что в коде. Обновляй при изменении структуры.

## Frontend Base: `frontend/app/`

### Composables (`frontend/app/composables/`)

| Файл | Экспорт | Назначение |
|------|---------|------------|
| `useAPI.ts` | `useAPI()` | HTTP клиент (mock/real). Методы: get, post, put, del. Токен + initData в заголовках |
| `useAuth.ts` | `useAuth()` | Auth: login (TMA init_data + OAuth), logout, switchRole. State: token, user |
| `useError.ts` | `useGlobalError()` | Глобальные ошибки. showError/clearError. Автоскрытие через 5с |
| `useNetwork.ts` | `useNetwork()` | Online/offline статус. isOnline |
| `useStorage.ts` | `useStorage()` | localStorage wrapper. JSON serialize. get/set/remove/clear |
| `useTg.ts` | `useTg()` | Telegram SDK: isInTelegram, webApp, initData, BackButton, MainButton, hapticFeedback, supportsVersion |

### Components (`frontend/app/components/`)

| Файл | Назначение |
|------|------------|
| `AppHeader.vue` | Шапка приложения |
| `AppTitle.vue` | Заголовок страницы |
| `BackButton.vue` | Универсальная кнопка назад (TMA + browser) |
| `EmptyState.vue` | Пустое состояние списка |
| `ErrorMessage.vue` | Отображение ошибки |
| `LoadingSpinner.vue` | Спиннер загрузки |
| `ServiceCard.vue` | Карточка сервиса на главной |

### Pages (`frontend/app/pages/`)

| Роут | Файл | Назначение |
|------|------|------------|
| `/` | `index.vue` | Главная — выбор сервиса |
| `/auth/callback` | `auth/callback.vue` | OAuth callback |
| `/ui` | `ui.vue` | UI-шоукейс (dev) |

### Types (`frontend/app/types/`)

| Файл | Содержание |
|------|-----------|
| `api.ts` | ApiResponse, ApiError, PaginatedResponse, User, Role, AuthResponse |
| `forms.ts` | FormField, FormState, ValidationRule, FormConfig |
| `telegram.d.ts` | Telegram WebApp типы |
| `ui.ts` | ButtonVariant, ButtonSize, Toast, ModalProps |

### Utils (`frontend/app/utils/`)

| Файл | Функции |
|------|---------|
| `formatters.ts` | formatPrice, formatDate, formatDateFull, formatTime, formatRelativeTime, getInitials, formatDriverName, randomId, debounce |
| `validators.ts` | isValidPhone, isValidPrice, isValidAddress, isValidDate, isValidTime, isValidSeats, isValidComment, isValidTelegramUsername |

### Config (`frontend/app/config/`)

| Файл | Назначение |
|------|-----------|
| `api.config.ts` | `USE_MOCK_API = true/false`. MOCK_CONFIG: errorRate, delays |
| `mockData.ts` | MOCK_USERS, AyaniOrder, CITY_ROUTES, INTERCITY_ROUTES, generateMockOrders, mockApiResponses |

### Plugins (`frontend/app/plugins/`)

| Файл | Назначение |
|------|-----------|
| `init.ts` | Telegram SDK ready + auto-login при старте |
| `error-handler.ts` | Глобальный перехват Vue errors + unhandled rejections |

### Middleware (`frontend/app/middleware/`)

| Файл | Назначение |
|------|-----------|
| `auth.ts` | Редирект на `/` если не авторизован. Usage: `definePageMeta({ middleware: 'auth' })` |

### Layouts (`frontend/app/layouts/`)

| Файл | Назначение |
|------|-----------|
| `default.vue` | Основной лейаут |

---

## Service Layers: `frontend/services/`

### AYAN (`frontend/services/ayan/`) — Попутки

| Файл | Назначение |
|------|-----------|
| `nuxt.config.ts` | Минимальный (compatibilityDate) |
| `README.md` | Концепция, planned structure |

**Реализация**: `frontend/app/` (composables useAPI, mock данные в mockData.ts)
**Pages**: Пока нет отдельных AYAN страниц
**API контракт**: `vault/wiki/services/ayan/api-contract.md`

### TAL (`frontend/services/tal/`) — Запись к мастерам

| Файл | Назначение |
|------|-----------|
| `nuxt.config.ts` | Layer config |
| `package.json` | iind-tal v0.1.0 |
| `README.md` | Концепция, API, архитектура |
| `app/composables/useTalAPI.ts` | TAL API клиент |
| `app/composables/useTalStore.ts` | TAL store |
| `app/pages/tal.vue` | Parent wrapper → /tal |
| `app/pages/tal-showcase.vue` | Шоукейс |

### AGAL (`frontend/services/agal/`) — Доставка

| Файл | Назначение |
|------|-----------|
| `nuxt.config.ts` | Layer config |
| `package.json` | iind-agal v0.1.0 |
| `README.md` | Концепция |
| `app/pages/agal.vue` | Parent wrapper → /agal |

### UUS (`frontend/services/uus/`) — Услуги

| Файл | Назначение |
|------|-----------|
| `nuxt.config.ts` | Layer config |
| `package.json` | iind-uus v0.1.0 |
| `README.md` | Концепция |
| `app/pages/uus.vue` | Parent wrapper → /uus |

---

## Backend: `backend/`

**Статус**: Scaffold-only. Нет миграций, нет моделей (.gitkeep).

### Controllers

| Файл | Назначение |
|------|-----------|
| `AuthController.php` | Login via Telegram (mock) |
| `UserController.php` | me, switchRole (mock) |
| `Ayan/OrderController.php` | AYAN orders (mock) |

### Routes (`backend/routes/api.php`)

**Public:**
- `GET /api/health` — health check
- `POST /api/auth/telegram` — TMA login

**Auth (sanctum):**
- `GET /api/user` — текущий юзер
- `POST /api/user/switch-role` — смена роли
- AYAN: `POST /ayan/orders`, `GET /ayan/orders/open`, `GET /ayan/orders/my`, `POST /ayan/orders/{id}/accept|complete|cancel`
- TAL: `GET /tal/services`, `GET /tal/masters`, `GET /tal/slots`, `POST /tal/bookings`, `DELETE /tal/bookings/{id}`
- UUS: `POST /uus/tasks`, `GET /uus/tasks/open`, `POST /uus/tasks/{id}/respond`
- AGAL: `POST /agal/parcels`, `GET /agal/parcels/open`, `POST /agal/parcels/{id}/take`

---

## API Implementation Status

| Сервис | Backend | Frontend | Mock Data |
|--------|---------|----------|-----------|
| AYAN | scaffold (OrderController mock) | нет страниц | mockData.ts |
| TAL | routes only (нет контроллеров) | showcase | нет |
| UUS | routes only | placeholder | нет |
| AGAL | routes only | placeholder | нет |
| Auth | scaffold (mock) | useAuth + init.ts | mockData.ts |

---

## Ключевые файлы-входы

- `frontend/nuxt.config.ts` — Nuxt конфиг, extends слоёв
- `frontend/app/app.vue` — Root component
- `frontend/app/config/api.config.ts` — Mock/real toggle
- `backend/routes/api.php` — Все API маршруты
- `vault/wiki/services/ayan/api-contract.md` — Финальный API контракт AYAN
