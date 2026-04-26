# CODE_MAP — Инвентарь кода iindiinda-app

> ИИ: читай этот файл чтобы понимать где что в коде. Обновляй при изменении структуры.

## Frontend Base: `frontend/app/`

### Composables (`frontend/app/composables/`)

| Файл | Экспорт | Назначение |
|------|---------|------------|
| `useAPI.ts` | `useAPI()` | HTTP клиент (mock/real). Методы: get, post, put, del. Токен + initData в заголовках |
| `useAuth.ts` | `useAuth()` | Auth: login (TMA init_data), logout, switchRole. Browser OAuth auto-login сейчас intentionally disabled до real backend support; для local dev можно дать `public.devInitData` fallback |
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
| `AppBottomNav.vue` | Нижняя навигация приложения |
| `EmptyState.vue` | Пустое состояние списка |
| `ErrorMessage.vue` | Отображение ошибки |
| `LoadingSpinner.vue` | Спиннер загрузки |
| `ServiceCard.vue` | Карточка сервиса на главной |
| `AppServiceAbout.vue` | Сворачиваемый блок "что это за сервис + примеры" для экранов сервисов |

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
| `auth.ts` | frontend auth helpers: `canUseDevInitData`, `getAyanAccessState` |
| `validators.ts` | isValidPhone, isValidPrice, isValidAddress, isValidDate, isValidTime, isValidSeats, isValidComment, isValidTelegramUsername |

### Config (`frontend/app/`)

| Файл | Назначение |
|------|-----------|
| `app.config.ts` | UI-оверрайды: primary=cyan, neutral=gray. Единый конфиг |
| `config/api.config.ts` | `USE_MOCK_API = true/false`. Сейчас `false` для реального AYAN backend. MOCK_CONFIG: errorRate, delays |
| `config/mockData.ts` | MOCK_USERS, CITY_ROUTES, mockApiResponses |

### Testing (`frontend/`)

| Файл | Назначение |
|------|-----------|
| `vitest.config.ts` | Базовый Vitest config для plain TS unit tests (`environment: node`, `tests/**/*.test.ts`) |
| `tests/unit/validators.test.ts` | Smoke unit tests для базовых валидаторов |

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
| `app/types/ayan.ts` | Типы: AyanTrip, AyanRequest, AyanResponse, DTO |
| `app/config/ayanMock.ts` | Mock генерация trips/requests/responses |
| `app/composables/useAyanTrips.ts` | CRUD поездок (fetchTrips, fetchTrip, createTrip, updateTrip) |
| `app/composables/useAyanRequests.ts` | CRUD запросов (fetchRequests, createRequest) |
| `app/composables/useAyanResponses.ts` | Отклики (fetch/create/cancel) |
| `app/composables/useAyanMy.ts` | Мои данные (myTrips, myRequests, myResponses) |
| `app/utils/role.ts` | AYAN role helpers: `getAyanCreateMode`, `isAyanPrimaryRole` |
| `app/pages/ayan.vue` | Parent wrapper → /ayan (только <NuxtPage />) |
| `app/pages/ayan/index.vue` | Лента поездок/запросов + табы + кнопка создания + role switcher |
| `app/components/AyanRoleSwitch.vue` | Переключатель `passenger/driver` через `/api/user/switch-role` |
| `app/components/AyanAccessState.vue` | Экран доступа для guest browser / failed Telegram auth в AYAN |
| `app/components/AyanCreateSlideover.vue` | Единый slideover создания поездки/запроса (pill-табы, side=bottom) |
| `app/pages/ayan/trip/[id].vue` | Детали поездки + отклик |
| `app/pages/ayan/request/[id].vue` | Детали запроса + отклик |

**API контракт**: `vault/wiki/services/ayan/api-contract.md`
**Composables**: используют корневой `useAPI()` для HTTP, mock данные в `ayanMock.ts`

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

**API contract**: `vault/wiki/services/agal/api-contract.md`

### UUS (`frontend/services/uus/`) — Услуги

| Файл | Назначение |
|------|-----------|
| `nuxt.config.ts` | Layer config |
| `package.json` | iind-uus v0.1.0 |
| `README.md` | Концепция |
| `app/pages/uus.vue` | Parent wrapper → /uus |

---

## Backend: `backend/`

**Статус**: In progress. Laravel runtime восстановлен, AYAN contract-aligned backend поднят на VPS и переведён на MySQL persistence. Реальная Telegram `initData` verification ещё не finished.

### Controllers

| Файл | Назначение |
|------|-----------|
| `AuthController.php` | Login via Telegram → create/find `User`, issue real Sanctum token. Сейчас `init_data = test` / parse stub, не prod-grade verification |
| `UserController.php` | `GET /api/user`, `POST /api/user/switch-role` через authenticated Sanctum user |
| `Ayan/OrderController.php` | AYAN orders (mock) |
| `Ayan/TripController.php` | Реальные AYAN trips endpoints через Eloquent/MySQL |
| `Ayan/RequestController.php` | Реальные AYAN requests endpoints через Eloquent/MySQL |
| `Ayan/ResponseController.php` | Реальные AYAN responses endpoints через Eloquent/MySQL, accept/reject, delete own response |
| `Ayan/MyController.php` | Реальные my trips/requests/responses через authenticated user |
| `Ayan/Concerns/SerializesAyanData.php` | Сериализация payload shape под фронтовые типы AYAN |
| `Http/Middleware/ForceJsonResponse.php` | Форсит JSON transport для `api` middleware group, чтобы guest protected routes возвращали `401`, не HTML redirect |

### Runtime Base

| Файл/папка | Назначение |
|-----------|------------|
| `artisan` | Laravel CLI entry point |
| `bootstrap/app.php` | Laravel app bootstrap + `api` routing + guest auth JSON handling |
| `bootstrap/providers.php` | Provider registry |
| `composer.json`, `composer.lock` | Laravel backend dependencies |
| `config/*.php` | Laravel runtime config |
| `public/index.php` | Front controller |
| `routes/console.php`, `routes/web.php` | Console/web routes |
| `resources/` | Laravel default resources scaffold |
| `storage/` | Laravel writable dirs scaffold |
| `tests/` | Backend PHPUnit feature/unit tests |

### Routes (`backend/routes/api.php`)

**Public:**
- `GET /api/health` — health check
- `POST /api/auth/telegram` — TMA login

**Auth (sanctum):**
- `GET /api/user` — текущий юзер
- `POST /api/user/switch-role` — смена роли
- AYAN: `GET/POST /api/ayan/trips`, `GET/PATCH /api/ayan/trips/{id}`
- AYAN: `GET/POST /api/ayan/requests`, `GET/PATCH /api/ayan/requests/{id}`
- AYAN: `GET/POST /api/ayan/trips/{id}/responses`
- AYAN: `GET/POST /api/ayan/requests/{id}/responses`
- AYAN: `PATCH /api/ayan/responses/{id}`, `DELETE /api/ayan/responses/{id}`
- AYAN: `GET /api/ayan/my/trips`, `GET /api/ayan/my/requests`, `GET /api/ayan/my/responses`
- TAL: `GET /tal/services`, `GET /tal/masters`, `GET /tal/slots`, `POST /tal/bookings`, `DELETE /tal/bookings/{id}`
- UUS: `POST /uus/tasks`, `GET /uus/tasks/open`, `POST /uus/tasks/{id}/respond`
- AGAL: `POST /agal/parcels`, `GET /agal/parcels/open`, `POST /agal/parcels/{id}/take`

---

## API Implementation Status

| Сервис | Backend | Frontend | Mock Data |
|--------|---------|----------|-----------|
| AYAN | runtime-ready on VPS: migrations + Sanctum + persistence controllers; Telegram verification still stub | pages + composables + types, real API switched on | ayanMock.ts |
| TAL | routes only (нет контроллеров) | showcase | нет |
| UUS | routes only | placeholder | нет |
| AGAL | routes only | placeholder | нет |
| Auth | partial real: Sanctum token issuance + `/api/user`; Telegram verification still stub | useAuth + init.ts | mockData.ts |

---

## Ключевые файлы-входы

- `frontend/nuxt.config.ts` — Nuxt конфиг, extends слоёв
- `frontend/.env` — local-only env для frontend dev (`NUXT_PUBLIC_API_BASE`, optional `NUXT_PUBLIC_DEV_INIT_DATA`)
- `frontend/app/app.vue` — Root component
- `frontend/app/app.vue` — overlay loader сейчас закомментирован, активен только `NuxtLoadingIndicator`
- `frontend/app/config/api.config.ts` — Mock/real toggle
- `backend/routes/api.php` — Все API маршруты
- `vault/wiki/services/ayan/api-contract.md` — Финальный API контракт AYAN

## Audit Notes — 2026-04-23

- `empty-template/`: new minimal starter template added inside the repository; contains starter `frontend/`, starter `backend/`, and its own full `vault/` workflow skeleton
- `frontend/app/config/api.config.ts`: `USE_MOCK_API = false` — AYAN фронт переключён на real API
- `frontend/app/composables/useAuth.ts`: browser auto-login не ходит в старый OAuth flow до появления backend support
- `ops/nginx/iind-vps-default.conf`: repo-tracked VPS Nginx config for SPA at `/` + Laravel API at `/api/*`
- local frontend against VPS: задать `frontend/.env` с `NUXT_PUBLIC_API_BASE=http://89.22.226.34/api`; для browser-only smoke path можно добавить `NUXT_PUBLIC_DEV_INIT_DATA=test`
- GitHub Pages deploy live: `https://iindev-solutions.github.io/iindiinda-app/` и `/ayan` отвечают `200`, rebased assets под `/iindiinda-app/assets/*` грузятся
- direct VPS smoke подтверждён: два synthetic Telegram payload юзера прошли login → create trip/request → respond → accept → `my/*`
- `backend/`: Laravel runtime восстановлен и проверен на VPS (`Nginx + PHP-FPM + MySQL`)
- `backend/routes/api.php`: AYAN routes уже совпадают с фронтовым контрактом `trips / requests / responses / my/*`
- `backend/app/Http/Controllers/Ayan/*`: persistence-backed, не sample arrays
- `backend/app/Http/Middleware/ForceJsonResponse.php`: guest protected API даёт JSON `401`, не `Route [login] not defined`
- `backend/app/Http/Controllers/AuthController.php`: real Sanctum token issuance уже есть, но Telegram verification ещё не production-ready
- deploy caveat: `public.devInitData` сериализуется в static HTML как пустой ключ; не собирать deploy с непустым `NUXT_PUBLIC_DEV_INIT_DATA`

## Audit Notes - 2026-04-24 19:50

- Legal helper shipped: `frontend/app/utils/legal.ts`
- Shared legal links component shipped: `frontend/app/components/AppLegalLinks.vue`
- Legal routes shipped: `frontend/app/pages/legal/ayan-terms.vue`, `frontend/app/pages/legal/privacy.vue`, `frontend/app/pages/legal/ayan-safety.vue`
- Lifecycle status model shipped end-to-end: `open|matched|completed|cancelled`
- Lifecycle backend migration added: `backend/database/migrations/2026_04_24_120000_expand_ayan_target_statuses.php`
- Lifecycle backend behavior updated in:
  - `backend/app/Http/Controllers/Ayan/TripController.php`
  - `backend/app/Http/Controllers/Ayan/RequestController.php`
  - `backend/app/Http/Controllers/Ayan/ResponseController.php`
  - `backend/app/Http/Controllers/Ayan/MyController.php`
  - `backend/app/Http/Controllers/Ayan/Concerns/SerializesAyanData.php`
- Lifecycle frontend behavior updated in:
  - `frontend/services/ayan/app/types/ayan.ts`
  - `frontend/services/ayan/app/composables/useAyanRequests.ts`
  - `frontend/services/ayan/app/pages/ayan/index.vue`
  - `frontend/services/ayan/app/pages/ayan/trip/[id].vue`
  - `frontend/services/ayan/app/pages/ayan/request/[id].vue`
  - `frontend/i18n/locales/ru.json`
  - `frontend/i18n/locales/sah.json`
- New API error helper and coverage:
  - `frontend/app/utils/api-error.ts`
  - `frontend/tests/unit/apiError.test.ts`
- Backend lifecycle regression coverage expanded in `backend/tests/Feature/AyanPersistenceTest.php`

## Audit Notes - 2026-04-25 10:50

- `frontend/app/utils/telegram.ts`: new Telegram bootstrap helpers `getTelegramWebApp`, `waitForTelegramWebApp`, and `waitForTelegramInitData`
- `frontend/app/composables/useTg.ts`: now stores reactive Telegram state snapshots instead of relying on one-shot computed reads from `window.Telegram.WebApp`
- `frontend/app/plugins/init.ts`: now watches delayed `WebApp`/`initData` arrival to call `ready()`/`expand()` and retry Telegram auto-login
- `frontend/app/composables/useAuth.ts`: mock auth still goes through shared mock API path, while real Telegram login keeps form-urlencoded `init_data` transport

## Audit Notes - 2026-04-25 14:30

- Platform legal center expanded in source:
  - `frontend/app/pages/legal/index.vue`
  - `frontend/app/pages/legal/user-agreement.vue`
  - `frontend/app/pages/legal/data-consent.vue`
  - `frontend/app/pages/legal/support.vue`
  - `frontend/app/pages/legal/uus-rules.vue`
  - `frontend/app/pages/legal/tal-rules.vue`
  - `frontend/app/pages/legal/agal-rules.vue`
- Shared legal document renderer added:
  - `frontend/app/components/LegalDocumentPage.vue`
- Legal helper now supports platform + all service scopes:
  - `frontend/app/utils/legal.ts`
  - `frontend/app/components/AppLegalLinks.vue`
- Current legal navigation decision: expose legal center from main menu/home bottom card only; do not repeat legal CTA blocks across service landing screens
- Frontend Russian locale now contains platform-level legal pack and service-specific rules:
  - `frontend/i18n/locales/ru.json`
- Placeholder marketing copy was tightened to safer legal posture in:
  - `frontend/services/uus/app/pages/uus.vue`
  - `frontend/services/tal/app/pages/tal.vue`
  - `frontend/services/agal/app/pages/agal.vue`
- RF legal/compliance audit captured in:
  - `vault/wiki/architecture/legal-rf-audit.md`

## Audit Notes - 2026-04-25 12:42

- `frontend/app/composables/useAPI.ts`: HTTPS runtime now normalizes insecure absolute API bases back to same-origin `/api`
- `frontend/app/utils/api-base.ts`: shared runtime/static API-base guard helpers
- `frontend/app/utils/telegram.ts`: default wait window increased to tolerate slower Telegram `WebApp`/`initData` bootstrap on real mobile clients
- `frontend/tests/unit/apiBase.test.ts`: regression coverage for runtime API-base normalization and static HTML guard behavior
- `frontend/tests/unit/telegram.test.ts`: regression coverage for slower Telegram bootstrap timings
- `frontend/scripts/build-static.mjs`: guarded static build wrapper that forces `NUXT_PUBLIC_API_BASE=/api` during VPS-style static builds
- `frontend/scripts/verify-static-api-base.mjs`: post-build guard that fails if generated HTML bakes any insecure absolute `apiBase`
- `frontend/.env`: treat as local-only frontend dev convenience; never trust it for production static deploys
