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

### Think First, Code Later

**Before ANY implementation:**
1. Read relevant vault doc — understand WHY
2. Write TODO in chat — what, why, problems to solve
3. Think 3 times — edge cases, state, API contract
4. Only then implement

**Never start coding without knowing:**
- What problem does this solve?
- What happens before/after?
- What states exist?

### Workflow Sequence

```
1. Read vault/master_index.md → find relevant vision/design
2. Read vault/wiki/architecture/*.md → understand context
3. Write TODO in chat:
   ### TODO: [feature name]
   - Problem: ...
   - Solution: ...
   - Edge cases: ...
4. Implement
5. Update vault/logs/changelog.md
6. Verify: typecheck → lint → test
```

### TODO Format (MANDATORY)

Every task starts with:
```
### TODO: [feature]
**Problem:** What problem does this solve?
**Solution:** How will we solve it?
**Edge cases:** What could go wrong?
```

Every completed task:
```
### DONE: [feature] ✅
- What changed
- What to test
```

### After Changes

- Update `vault/logs/changelog.md` (date, what, why)
- Cite vault source: `Based on: vault/wiki/architecture/ayan-vision.md`

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
