# AGENTS.md — iindiinda-app

Telegram Mini App платформа с 4 сервисами (такси, мастера, доставка, бронирование). Монорепозиторий: Nuxt 4 (frontend) + Laravel (backend).

## Project Management (Vault)

**Knowledge vault**: `vault/` (in repo) — единое место документации, анализа и имплементации
- **Workflow**: сначала думаем в vault → анализируем → через vault выполняем задачи
- Project status, roadmap, TODOs, decisions, design docs → **всё в vault**
- AGENTS.md = technical reference only

### Vault Structure
- `vault/raw/` — сырые данные (аудиты, спецификации)
- `vault/wiki/architecture/` — системный дизайн, API контракт, модели, auth flow
- `vault/wiki/services/` — документация по сервисам (Laravel API, Nuxt app)
- `vault/master_index.md` — карта базы знаний (WikiLinks)
- `vault/logs/changelog.md` — история изменений
- Дизайн переписывания: `vault/wiki/architecture/ayan-rewrite-design.md`

## Frontend Path: `frontend/` 

All frontend paths are relative to `frontend/`:
- Nuxt config: `frontend/nuxt.config.ts`
- App code: `frontend/app/` (composables, pages, types, layouts, assets, utils, middleware)
- Service layers: `frontend/services/{ayan,uus,agal,tal}/`
- i18n: `frontend/i18n/locales/{ru,sah}.json`

## Architecture

### Frontend: Nuxt 4 with Layer Extends

```
frontend/
├── nuxt.config.ts              # extends: [./services/agal, ./services/ayan, ./services/tal, ./services/uus]
├── app/                        # Base app — composables, pages, types, layouts
│   ├── composables/            # useAuth, useTg, useAPI, useTaxiAPI, usePolling, useOrderStatus (auto-imported)
│   ├── utils/                  # format.ts (formatPrice, formatDate)
│   ├── middleware/             # auth.global.ts (auto-login)
│   ├── config/api.config.ts    # Mock toggle (RUNTIME via env, not compile-time)
│   ├── pages/                  # index.vue (hub), ui.vue (style guide)
│   ├── types/api.ts            # TypeScript models + ITaxiAPI interface
│   └── assets/css/             # Design system (cyan theme, dark mode)
├── services/                   # Each is a Nuxt layer extending the base
│   ├── ayan/                   # Такси (Taxi) — only active service
│   │   └── app/
│   │       ├── pages/ayan/     # 7 pages (index, create, my-order, driver, orders, active-ride, complete)
│   │       └── components/    # Shared: OrderCard, StatusBadge, DriverInfo, PriceDisplay, RatingStars
│   ├── uus/                    # Мастера (Masters) — stub
│   ├── agal/                   # Доставка (Delivery) — stub
│   └── tal/                    # Бронирование (Booking) — stub
└── i18n/locales/               # ru.json, sah.json
```

- **SSR disabled** — `ssr: false`; SPA-only
- Service layer pages live at `services/{name}/app/pages/`

### Service Names (Yakut → English)

| Code | Yakut    | English     | Description           |
|------|----------|-------------|-----------------------|
| ayan | Бардыбыт | Taxi        | Passenger sets price  |
| uus  | Уус      | Masters     | Repair, handyman      |
| agal | Аҕал     | Delivery    | Air parcel delivery   |
| tal  | Тал      | Booking     | Salon/clinic booking  |

### Backend: Laravel (Scaffold Only)

- **Routes**: `backend/routes/api.php` — single source of truth for all API contracts
- **Controllers/Models**: Empty (`.gitkeep` only) — routes are defined but unimplemented
- **Auth**: `POST /api/auth/telegram` with `init_data` → `{token, user}`
- **Pattern**: `/api/{service}/{resource}` — e.g., `/api/ayan/orders`, `/api/tal/bookings`

### Composables Architecture

| Composable | Purpose |
|------------|---------|
| `useAPI` | Base HTTP client ($fetch wrapper, slim) |
| `useAuth` | Auth + auto-login + token persistence (localStorage + useState) |
| `useTg` | Telegram WebApp SDK (with ready(), expand(), cleanup) |
| `useTaxiAPI` | Factory: returns `ITaxiAPI` implementation (real or mock) |
| `usePolling` | Smart polling with exponential backoff + visibility API |
| `useOrderStatus` | Status config map + label helpers (shared across pages) |

### ITaxiAPI Interface

All ayan pages use `ITaxiAPI` methods exclusively — never call `useAPI` directly:

```typescript
interface ITaxiAPI {
  createOrder(data: CreateTaxiOrderRequest): Promise<TaxiOrder>
  getOpenOrders(page?: number): Promise<PaginatedResponse<TaxiOrder>>
  getMyOrder(): Promise<TaxiOrder | null>
  getOrder(id: number): Promise<TaxiOrder>
  acceptOrder(id: number): Promise<TaxiOrder>
  markArrived(id: number): Promise<TaxiOrder>
  startTrip(id: number): Promise<TaxiOrder>
  completeTrip(id: number, rating?: number): Promise<TaxiOrder>
  cancelOrder(id: number, reason?: string): Promise<TaxiOrder>
  getUser(): Promise<User>
  switchRole(role: 'passenger' | 'driver'): Promise<User>
  setAvailability(isAvailable: boolean): Promise<User>
  login(initData: string): Promise<AuthResponse>
}
```

Two implementations: `TaxiApiClient` (real API) and `TaxiApiMock` (localStorage).

### Order Status Flow (6 statuses)

```
open → accepted → arrived → in_progress → completed
 │        │          │            │
 └──→ cancelled ←───┴────────────┘
```

| From → To | Who | Endpoint |
|-----------|-----|----------|
| open → accepted | Driver | `POST /ayan/orders/{id}/accept` |
| open → cancelled | Passenger | `POST /ayan/orders/{id}/cancel` |
| accepted → arrived | Driver | `POST /ayan/orders/{id}/arrive` |
| accepted → cancelled | Passenger | `POST /ayan/orders/{id}/cancel` |
| arrived → in_progress | Driver | `POST /ayan/orders/{id}/start` |
| arrived → cancelled | Passenger | `POST /ayan/orders/{id}/cancel` |
| in_progress → completed | Driver | `POST /ayan/orders/{id}/complete` |
| in_progress → cancelled | Emergency | `POST /ayan/orders/{id}/cancel` |

## Dev Commands

```bash
cd frontend

npm run dev          # All services layered → http://localhost:3000
npm run build        # Production build
npm run typecheck    # nuxt typecheck (vue-tsc)
npm run lint         # eslint .
npm run lint:fix     # eslint . --fix
npm run format       # prettier --check .
npm run format:fix   # prettier --write .
```

**Broken scripts** (reference non-existent `layers/` dirs): `dev:taxi`, `dev:masters`, `dev:core` — do NOT use these.

## Mock API

Controlled via `NUXT_PUBLIC_USE_MOCK_API` env var (RUNTIME, no restart needed):

```bash
# .env
NUXT_PUBLIC_USE_MOCK_API=true   # → TaxiApiMock (localStorage-backed)
NUXT_PUBLIC_USE_MOCK_API=false  # → TaxiApiClient (real Laravel API)
```

- `useTaxiAPI()` returns `ITaxiAPI` — pages never know which implementation
- Both implementations share identical return types
- Mock includes: simulated delays, error rate, auto-accept orders, full status machine
- On localhost (not in Telegram): mock auth auto-creates test user

## Auth Flow

### Auto-login (from middleware `auth.global.ts`)
1. Check `isAuthenticated` (token in useState + localStorage)
2. If in Telegram: read `initData` → `POST /auth/telegram`
3. If on localhost: mock auth auto-creates user with selected role
4. Token persists in `localStorage` + `useState`
5. All subsequent requests: `Authorization: Bearer {token}`

### Role Selection (MVP)
First visit to `/ayan` → choose Passenger or Driver → `POST /user/switch-role`

## Quick Start

```bash
cp .env.example .env

# Frontend
cd frontend
npm install
npm run dev

# Backend (Docker) — currently scaffold-only
docker-compose up -d    # API at http://localhost:8000
```

## Code Style (differs from defaults)

- **Prettier**: tabs (not spaces), `tabWidth: 4`, single quotes, no semicolons, `trailingComma: "none"`, `printWidth: 120`
- **ESLint**: `@typescript-eslint/no-explicit-any: off`, `vue/no-v-html: off`
- **TypeScript**: strict, checked via `nuxt typecheck`
- **Composables**: auto-imported from `app/composables/` — no manual imports needed

## Nuxt 4 Page Structure (MANDATORY)

Service pages use nested directory structure for file-based routing:

```
services/ayan/app/pages/
├── ayan.vue                    → /ayan  (PARENT — ONLY <NuxtPage />, no UI/logic)
└── ayan/
    ├── index.vue               → /ayan          (hub + role selection)
    ├── create.vue              → /ayan/create   (create order)
    ├── my-order.vue            → /ayan/my-order (passenger: track order)
    ├── driver.vue              → /ayan/driver   (driver: dashboard)
    ├── orders.vue              → /ayan/orders   (driver: open orders)
    ├── active-ride.vue         → /ayan/active-ride (driver: active ride)
    └── complete.vue            → /ayan/complete (both: rating)
```

- Same pattern for all services: `uus/`, `agal/`, `tal/`
- Parent wrapper (`ayan.vue`) must contain ONLY `<NuxtPage />`

## Code Rules (MANDATORY)

- **Navigation**: ALWAYS `navigateTo('/path')` — NEVER `router.push()`, NEVER `useRouter()`. `navigateTo()` is Nuxt-native and works with layer routes; `router.push` breaks.
- **Tailwind classes**: NEVER dynamic interpolation like `bg-${color}-500/20` — JIT can't detect them. Use static class maps (e.g., `bgClass: 'bg-cyan-500/20'`)
- **Telegram SDK version checks**: HapticFeedback/BackButton require v6.1+. Always call `supportsVersion('6.1')` BEFORE accessing these — SDK throws on property access, optional chaining doesn't help
- **API URL construction**: NEVER `new URL(endpoint, base)` — drops path segments when endpoint starts with `/`. Use string concatenation
- **Don't modify Telegram SDK** — loaded externally via `https://telegram.org/js/telegram-web-app.js`
- **Don't create `layers/` folder** — services are in `services/`
- **Don't forget `initData`** — all API calls need it until auth completes
- **ITaxiAPI only** — ayan pages must use `useTaxiAPI()` methods, never `useAPI()` directly
- **Shared components** — extract duplicates into `services/ayan/app/components/` (OrderCard, StatusBadge, etc.)
- **usePolling** — all polling must use `usePolling` composable with backoff, never raw `setInterval`/`useIntervalFn`

## API Endpoints (Ayan)

### Auth
| Method | Path | Description |
|--------|------|-------------|
| POST | /api/auth/telegram | Login via Telegram initData |

### User
| Method | Path | Description |
|--------|------|-------------|
| GET | /api/user | Current user (NOT /user/me) |
| POST | /api/user/switch-role | Switch role `{role: passenger\|driver}` |
| GET | /api/user/availability | Driver availability |
| POST | /api/user/availability | Set availability `{is_available: bool}` |

### Orders
| Method | Path | Description |
|--------|------|-------------|
| POST | /api/ayan/orders | Create order |
| GET | /api/ayan/orders/open | Open orders (NOT /ayan/orders) |
| GET | /api/ayan/orders/my | My active order (NOT /ayan/orders/active) |
| POST | /api/ayan/orders/{id}/accept | Accept order |
| POST | /api/ayan/orders/{id}/arrive | Mark arrived |
| POST | /api/ayan/orders/{id}/start | Start trip |
| POST | /api/ayan/orders/{id}/complete | Complete trip `{rating?}` |
| POST | /api/ayan/orders/{id}/cancel | Cancel order `{reason?}` |

## API Auth Pattern

```typescript
// Initial auth: Telegram initData header
headers['X-Telegram-Init-Data'] = initData.value

// After login: Bearer token
headers['Authorization'] = `Bearer ${token}`
```

## Design System

- **Theme**: Dark only (`colorMode: {preference: 'dark', fallback: 'dark'}`)
- **Primary**: Cyan `#5edac6`, Neutral: Gray
- **Background**: `#0a0c0e`
- **Font**: Geist, Inter
- **UI**: @nuxt/ui v4, `colors: ['cyan', 'gray']`

## i18n

- **Default**: Russian (`ru`), Secondary: Yakut/Sakha (`sah`)
- **Strategy**: `no_prefix` (no URL locale prefix)
- **Files**: `frontend/i18n/locales/{ru,sah}.json`
- **Structure**: Nested keys only — `ayan.order.status.open.title` not `ayan.orderStatusOpen`
- **No hardcoded strings** — all user-visible text must use `t('key')`

## Environment

Required in `.env` (root):
```bash
BACKEND_PORT=8000
DB_DATABASE=iindapp
DB_USERNAME=iindapp
DB_PASSWORD=secret
TELEGRAM_BOT_TOKEN=xxx
TELEGRAM_BOT_USERNAME=iindapp_bot
JWT_SECRET=xxx
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
NUXT_PUBLIC_USE_MOCK_API=true
```

Note: `backend/.env.example` uses different DB creds (`iind`/`iind`) — root `.env.example` is the canonical one.

## Rewrite Roadmap

Based on: `vault/wiki/architecture/ayan-rewrite-design.md`

1. **Phase 0: Foundation** — types/api.ts (ITaxiAPI), TaxiApiClient, TaxiApiMock, useAuth, useTg fixes, usePolling, auth middleware
2. **Phase 1: Passenger flow** — index (role select), create, my-order, complete
3. **Phase 2: Driver flow** — driver, orders, active-ride
4. **Phase 3: Shared & polish** — extract components, fix i18n, update README
