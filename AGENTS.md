# AGENTS.md — iindiinda-app

> iindiinda — делаем сложные вещи просто. Modern solutions. Simply.

**iind.app** — Telegram Mini App платформа. Один принцип для всех сервисов: заявка → отклик → договор. Без посредников. Без сложных систем.

Монорепозиторий: Nuxt 4 (frontend) + Laravel (backend).

## Vault — единое место документации и имплементации

**Workflow**: сначала думаем в vault → анализируем → через vault выполняем задачи

- `vault/master_index.md` — карта базы знаний (start here)
- `vault/wiki/architecture/` — системный дизайн, API контракт, модели, auth flow, статус-флоу
- `vault/wiki/services/` — документация по сервисам (эндпоинты, роутинг, composables)
- `vault/raw/` — сырые данные (аудиты, спецификации)
- `vault/logs/changelog.md` — история изменений
- `vault/CLAUDE.md` — важные правила 

Все решения, дизайн-доки, API детали, статус-флоу → **vault**. AGENTS.md = правила + how-to.

## Branches

- `main` — production, `dev` — разработка
- `front/ayan` — текущая разработка AYAN
- `front/taxi` — legacy (не поддерживается)

## Frontend Path: `frontend/`

- Nuxt config: `frontend/nuxt.config.ts`
- App code: `frontend/app/` (composables, pages, types, layouts, assets, utils, middleware)
- Service layers: `frontend/services/{ayan,uus,agal,tal}/`
- i18n: `frontend/i18n/locales/{ru,sah}.json`

## Nuxt 4 Extends — Service Layers

`nuxt.config.ts` extends services as Nuxt layers (in order):
```
services/ayan → services/agal → services/tal → services/uus
```
Each service has its own `app/` folder with pages, composables, components.
- **AYAN** (first) — primary service, must use `useTaxiAPI()` from `app/composables/`, never `useAPI()` directly
- **AGAL, TAL, UUS** — extended layers, use `useAPI()`

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

## Vault Workflow

**Start here:** `vault/master_index.md` — карта всей базы знаний, links на все важные файлы.

**Workflow:** Vision → Design Doc → Implementation → Changelog

1. **Before coding** — read relevant vision: `vault/wiki/architecture/ayan-vision.md`
2. **During work** — read service docs: `vault/wiki/architecture/system-design.md`
3. **After changes** — update `vault/logs/changelog.md` (date, what changed, why)
4. **Breadcrumb Rule** — cite vault source: `Based on: vault/wiki/architecture/ayan-vision.md`

**Vault structure:**
```
vault/
├── master_index.md        ← start here (links to everything)
├── CLAUDE.md              ← AI rules, Brainstorming skill
├── wiki/
│   └── architecture/      ← vision docs, system design
├── raw/                   ← raw data, specs, audits
└── logs/
    └── changelog.md       ← track structural changes
```

**WikiLinks in vault:** `[[wiki/architecture/ayan-vision]]` = file path. OpenCode can read these .md files directly — treat as cross-references, not broken links.

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
- **ITaxiAPI only** — ayan pages must use `useTaxiAPI()` methods, never `useAPI()` directly
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
