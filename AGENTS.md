# AGENTS.md — iindiinda-app

Telegram Mini App платформа с 4 сервисами (такси, мастера, доставка, бронирование). Монорепозиторий: Nuxt 4 (frontend) + Laravel (backend).

## Project Management (Obsidian)

**Master file**: `C:\Users\slavk\Desktop\my-data\zettel\slava-obsidian-new\base\iindev.md`
- Project status, roadmap, TODOs, decisions → **edit Obsidian, not AGENTS.md**
- AGENTS.md = technical reference only

## Frontend Path: `frontend/` 

All frontend paths are relative to `frontend/`:
- Nuxt config: `frontend/nuxt.config.ts`
- App code: `frontend/app/` (composables, pages, types, layouts, assets)
- Service layers: `frontend/services/{ayan,uus,agal,tal}/`
- i18n: `frontend/i18n/locales/{ru,sah}.json`

## Architecture

### Frontend: Nuxt 4 with Layer Extends

```
frontend/
├── nuxt.config.ts              # extends: [./services/agal, ./services/ayan, ./services/tal, ./services/uus]
├── app/                        # Base app — composables, pages, types, layouts
│   ├── composables/            # useTg, useAPI, useAuth, useTaxiAPI, useMockAPI (auto-imported)
│   ├── config/api.config.ts    # USE_MOCK_API toggle (currently true)
│   ├── pages/                  # index.vue (hub), ui.vue (style guide)
│   ├── types/api.ts            # TypeScript models matching backend API
│   └── assets/css/             # Design system (cyan theme, dark mode)
├── services/                   # Each is a Nuxt layer extending the base
│   ├── ayan/                   # Такси (Taxi) — only active service
│   ├── uus/                    # Мастера (Masters)
│   ├── agal/                   # Доставка (Delivery)
│   └── tal/                    # Бронирование (Booking)
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

`frontend/app/config/api.config.ts` controls mock vs real API:
- `USE_MOCK_API = true` (current) → taxi pages use `useMockAPI` (localStorage-backed, simulated delays/errors)
- `USE_MOCK_API = false` → uses real `useAPI` hitting the Laravel backend
- Toggle via `useTaxiAPI` composable — components don't need to change
- **Requires dev server restart** after toggling

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
    ├── index.vue               → /ayan          (landing page)
    ├── create.vue              → /ayan/create
    ├── driver.vue              → /ayan/driver
    └── ...
```

- Same pattern for all services: `uus/`, `agal/`, `tal/`
- Parent wrapper (`ayan.vue`) must contain ONLY `<NuxtPage />`

## Code Rules (MANDATORY)

- **Navigation**: ALWAYS `navigateTo('/path')` — NEVER `router.push()`, NEVER `useRouter()`. `navigateTo()` is Nuxt-native and works with layer routes; `router.push` breaks.
  - ⚠️ `frontend/app/pages/index.vue` currently uses `router.push()` — this is a known violation that should be fixed
- **Tailwind classes**: NEVER dynamic interpolation like `bg-${color}-500/20` — JIT can't detect them. Use static class maps (e.g., `bgClass: 'bg-cyan-500/20'`)
- **Telegram SDK version checks**: HapticFeedback/BackButton require v6.1+. Always call `supportsVersion('6.1')` BEFORE accessing these — SDK throws on property access, optional chaining doesn't help
- **API URL construction**: NEVER `new URL(endpoint, base)` — drops path segments when endpoint starts with `/`. Use string concatenation
- **Don't modify Telegram SDK** — loaded externally via `https://telegram.org/js/telegram-web-app.js`
- **Don't create `layers/` folder** — services are in `services/`
- **Don't forget `initData`** — all API calls need it until auth completes

## API Auth Pattern

```typescript
// Initial auth: Telegram initData header
headers['X-Telegram-Init-Data'] = initData.value

// After login: Bearer token
headers['Authorization'] = `Bearer ${token}`
```

Role switching: `POST /api/user/switch-role` with `{role: 'passenger' | 'driver'}`

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
```

Note: `backend/.env.example` uses different DB creds (`iind`/`iind`) — root `.env.example` is the canonical one.
