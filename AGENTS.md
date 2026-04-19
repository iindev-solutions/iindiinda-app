# AGENTS.md — iindiinda-app

> iindiinda — делаем сложные вещи просто.

**iind.app** — Telegram Mini App платформа. Один принцип: заявка → отклик → договор. Без посредников.

Монорепозиторий: Nuxt 4 (frontend) + Laravel (backend).

## Vault — источник документации

**Start here**: `vault/master_index.md`

| Путь | Содержание |
|------|-----------|
| `vault/master_index.md` | Карта базы знаний, roadmap, активные задачи |
| `vault/sprint.md` | Текущий спринт, статусы задач |
| `vault/CODE_MAP.md` | Инвентарь кода: composables, components, pages, API |
| `vault/wiki/architecture/` | Vision, system design, auth flow, roadmap |
| `vault/wiki/services/` | API контракты, эндпоинты, модели |
| `vault/raw/` | Черновики (пустой после обработки) |
| `vault/logs/changelog.md` | История изменений |

## Branches

- `main` — production, `dev` — разработка
- `front/ayan` — текущая разработка AYAN
- `front/taxi` — legacy

## Frontend: `frontend/`

- Nuxt config: `frontend/nuxt.config.ts`
- App code: `frontend/app/` (composables, pages, types, layouts, assets, utils, middleware)
- Service layers: `frontend/services/{ayan,agal,tal,uus}/`
- i18n: `frontend/i18n/locales/{ru,sah}.json`

## Nuxt 4 Extends — Service Layers

`nuxt.config.ts` extends: `services/ayan → services/agal → services/tal → services/uus`

Each service: `app/` folder с pages, composables, components.

## Dev Commands (frontend/)

```bash
npm run dev          # → http://localhost:3000
npm run build        # Production build
npm run typecheck    # nuxt typecheck (vue-tsc)
npm run lint         # eslint .
npm run lint:fix     # eslint . --fix
npm run format       # prettier --check .
npm run format:fix   # prettier --write .
```

## Quick Start

```bash
cp .env.example .env
cd frontend && npm install && npm run dev
docker-compose up -d    # Backend (scaffold-only) at http://localhost:8000
```

## Workflow

1. Read `vault/sprint.md` → текущие задачи
2. Read `vault/CODE_MAP.md` → где что в коде
3. Read `vault/wiki/` → контекст задачи (vision, API contract)
4. Implement
5. Verify: typecheck → lint → test
6. Обновить `vault/sprint.md` (статус задачи)
7. Обновить `vault/CODE_MAP.md` (если структура изменилась)
8. Обновить `vault/logs/changelog.md` (дата, что, зачем)

## Code Style

- **ESLint + Prettier**: `printWidth: 120`, `tabWidth: 4`, single quotes, no semicolons, `trailingComma: none`
- **ESLint overrides**: `@typescript-eslint/no-explicit-any: off`, `vue/no-v-html: off`
- **TypeScript**: strict, checked via `npm run typecheck`
- **Composables**: auto-imported from `app/composables/` — no manual imports

## Nuxt 4 Page Structure (MANDATORY)

```
services/ayan/app/pages/
├── ayan.vue                    → /ayan  (PARENT — ONLY <NuxtPage />, no UI/logic)
└── ayan/
    ├── index.vue               → /ayan
    ├── create.vue              → /ayan/create
    └── ...
```

- Same pattern for all services
- Parent wrapper must contain ONLY `<NuxtPage />`

## Code Rules (MANDATORY)

- **Navigation**: ALWAYS `navigateTo('/path')` — NEVER `router.push()`
- **Tailwind**: NEVER dynamic interpolation like `bg-${color}-500/20` — use static class maps
- **Telegram SDK**: HapticFeedback/BackButton require v6.1+ — always `supportsVersion('6.1')` BEFORE accessing
- **API URL**: NEVER `new URL(endpoint, base)` — use string concatenation
- **Don't modify Telegram SDK** — loaded externally
- **Don't create `layers/` folder** — services are in `services/`
- **Polling** — use `useIntervalFn` from `@vueuse/core` for polling, not raw `setInterval`
- **Shared components** — extract duplicates into `services/ayan/app/components/`
- **No hardcoded strings** — all user-visible text must use `t('key')`
- **i18n keys**: nested only — `ayan.order.status.open` not `ayan.orderStatusOpen`

## Design System

- Dark only, Primary: Cyan `#5edac6`, Background: `#0a0c0e`, Font: Geist/Inter
- @nuxt/ui v4, `colors: ['cyan', 'gray']`

## Environment

```bash
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

Mock/real API toggle: `frontend/app/config/api.config.ts` → `USE_MOCK_API = true/false` (compile-time, requires restart). Full .env: see root `.env.example`
