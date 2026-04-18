# AGENTS.md — iindiinda-app

Telegram Mini App платформа с 4 сервисами (такси, мастера, доставка, бронирование). Монорепозиторий: Nuxt 4 (frontend) + Laravel (backend).

## Project Documentation (Obsidian) — PRIMARY KNOWLEDGE BASE

**ALL project documentation, roadmap, and task tracking lives in Obsidian.**

**Master file**: `C:\Users\slavk\Desktop\my-data\zettel\slava-obsidian-new\base\iindev.md`
- Project overview, service links, roadmap with phases, TODO tasks
- **Important**: This is the single source of truth for project status and planning
- When adding TODOs or updating roadmap → **edit Obsidian file, not AGENTS.md**
- Services documented via wiki-links: [[iind.ayan]], [[iind.uus]], [[iind.aʃal]], [[iind.tal]]
- TODO format: `- [ ] task description` (supports ⏫ priority marker)
- Roadmap phases are tracked in Obsidian with checkboxes

**AGENTS.md** = technical reference only (architecture, commands, code style, env vars)
**Obsidian** = project management (roadmap, TODOs, status, decisions, specs)

**Rule**: If you're adding a task, updating project status, or documenting a decision → write to Obsidian first.

## Quick Start

```bash
# Root setup
cp .env.example .env

# Frontend (Nuxt 4 with layers)
cd frontend/tma-app
npm install
npm run dev          # http://localhost:3000

# Backend (Docker)
docker-compose up -d # API at http://localhost:8000
```

## Architecture

### Frontend: Nuxt 4 Layers
- **Base**: `frontend/tma-app/app/` — core app, layouts, composables
- **Services as Layers**: `services/{ayan,uus,agal,tal}/` — each extends base via `nuxt.config.ts extends`
- **Main config**: `frontend/tma-app/nuxt.config.ts` imports all 4 services via `extends: ['./services/agal', './services/ayan', './services/tal', './services/uus']`

**Service Names (Yakut)**:
- `ayan` = Бардыбыт (Taxi)
- `uus` = Уус (Masters)  
- `agal` = Аҕал (Delivery)
- `tal` = Тал (Booking)

### Backend: Laravel API
- **Routes**: `backend/routes/api.php` — **single source of truth** for all API contracts
- **Auth**: `POST /api/auth/telegram` with `init_data` → returns `{token, user}`
- **Pattern**: `/api/{service}/{resource}` — e.g., `/api/ayan/orders`, `/api/tal/bookings`

## Key Composables (Auto-imported)

```typescript
// Telegram WebApp SDK
const { initData, user, themeParams, ready, expand, hapticFeedback } = useTg()

// HTTP with auto-auth
const { get, post, put, del } = useAPI() 
// - Auto-injects `X-Telegram-Init-Data` header
// - Auto-adds Bearer token after auth

// Auth flow
const { login, logout, switchRole, isAuthenticated } = useAuth()
// login() calls POST /auth/telegram with initData
```

## Dev Commands

```bash
cd frontend/tma-app

# Main
npm run dev              # All services layered
npm run build            # Production build
npm run typecheck        # vue-tsc
npm run lint             # ESLint + Prettier check
npm run lint:fix         # Auto-fix

# Individual layers (legacy aliases, folders are services/ not layers/)
npm run dev:taxi         # = cd services/ayan && nuxt dev
npm run dev:masters      # = cd services/uus && nuxt dev
npm run dev:core         # = cd services/tal (legacy name)
```

## Design System

- **Theme**: Dark only (`colorMode: {preference: 'dark', fallback: 'dark'}`)
- **Primary**: Cyan `#5edac6`
- **Background**: `#0a0c0e`
- **Font**: Geist, Inter
- **UI**: @nuxt/ui v4 with `colors: ['cyan', 'gray']`

## API Integration

**Auth Header Pattern**:
```typescript
// Telegram Mini App initData for initial auth
headers['X-Telegram-Init-Data'] = initData.value

// Bearer token for subsequent requests
headers['Authorization'] = `Bearer ${token}`
```

**Role Switching**: `POST /api/user/switch-role` with `{role: 'passenger' | 'driver'}`

## i18n

- **Default**: Russian (`ru`)
- **Secondary**: Yakut/Sakha (`sah`)
- **Strategy**: `no_prefix` (no URL locale prefix)
- **Files**: `i18n/locales/{ru,sah}.json`

## Code Style

- **ESLint**: Configured via `eslint.config.mjs` — extends Nuxt + Prettier
- **Prettier**: `printWidth: 120` (not 80)
- **TypeScript**: Strict, Vue TSC for type checking
- **Composables**: Auto-imported from `app/composables/`

## Critical Files

| File | Purpose |
|------|---------|
| `backend/routes/api.php` | All API endpoints with inline docs |
| `frontend/tma-app/app/types/api.ts` | TypeScript models matching API |
| `frontend/tma-app/app/composables/useTg.ts` | Telegram WebApp SDK wrapper |
| `frontend/tma-app/nuxt.config.ts` | Layer orchestration, UI config |
| `docker-compose.yml` | PHP 8.2 + MySQL 8.0 |

## Environment

Required in `.env`:
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

## Nuxt 4 Page Structure (MANDATORY)

Service pages use **nested directory structure** for file-based routing:

```
services/ayan/app/pages/
├── ayan.vue                    → /ayan  (PARENT — just <NuxtPage />, no UI)
└── ayan/
    ├── index.vue               → /ayan          (main/landing page)
    ├── create.vue              → /ayan/create
    ├── driver.vue              → /ayan/driver
    ├── orders.vue              → /ayan/orders
    ├── my-order.vue            → /ayan/my-order
    ├── active-ride.vue         → /ayan/active-ride
    └── complete.vue            → /ayan/complete
```

**Rules**:
- `ayan.vue` = PARENT WRAPPER, contains ONLY `<NuxtPage />` — no layout, no UI, no logic
- `ayan/index.vue` = main page at `/ayan` (the landing page with buttons, etc.)
- Sub-pages = `ayan/{page}.vue` files inside the directory
- Same pattern for all services: `uus/`, `agal/`, `tal/`
- **ALWAYS use `navigateTo()`** — NEVER `router.push()` or `useRouter()`
- `navigateTo()` is Nuxt-native, supports middleware, layers, SSR; `router.push` breaks with layer routes

## Code Rules (MANDATORY)

- **Navigation**: ALWAYS `navigateTo('/path')` — NEVER `router.push()`, NEVER `useRouter()`
- **Tailwind classes**: NEVER dynamic interpolation like `bg-${color}-500/20` — Tailwind JIT can't detect them. Use full static class names in maps (e.g. `bgClass: 'bg-cyan-500/20'`)
- **Telegram version checks**: HapticFeedback requires v6.1+, BackButton requires v6.1+. Always check `supportsVersion('6.1')` before accessing these properties — the SDK throws on property access, optional chaining doesn't help
- **API URL construction**: NEVER use `new URL(endpoint, base)` — it drops path segments when endpoint starts with `/`. Use string concatenation instead

## Common Mistakes to Avoid

- **Don't create `layers/` folder** — services are in `services/`, legacy scripts reference wrong paths
- **Don't modify Telegram SDK** — loaded externally via `https://telegram.org/js/telegram-web-app.js`
- **Don't forget `initData`** — all API calls need it until auth completes
- **SSR disabled** — `ssr: false` in nuxt.config.ts; this is SPA-only
- **Don't use `router.push`** — always use `navigateTo()` for navigation
- **Don't use dynamic Tailwind classes** — JIT won't detect `bg-${var}-500`, use static class maps
