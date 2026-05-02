# AGENTS.md — iindiinda-app

> iindiinda — make hard things simple.

`iind.app` is a Telegram Mini App platform built around one principle: request -> response -> agreement.

Monorepo: Nuxt 4 frontend + Laravel backend.

## Vault Is The Source Of Truth

Start here: `vault/master_index.md`

`vault/` is the project knowledge base. It is not optional context. It is the canonical source of truth for:

- current project state
- active sprint work
- stop point and next step
- code inventory
- architectural decisions
- operational handoff notes

## Mandatory Vault Protocol

Every session must begin with these reads, in order:

1. `vault/master_index.md`
2. `vault/WORKFLOW.md`
3. `vault/sprint.md`
4. `vault/resume-plan.md`

Before starting a domain-specific task, also read the relevant docs under:

- `vault/wiki/architecture/`
- `vault/wiki/services/`
- `vault/CODE_MAP.md`

Before finishing any meaningful task, update `vault/`.

Minimum required update after meaningful work:

1. `vault/logs/changelog.md`

Update when relevant:

1. `vault/resume-plan.md` if the stop point, blocker, next step, or deployment state changed
2. `vault/sprint.md` if sprint status, blockers, or priorities changed
3. `vault/CODE_MAP.md` if code structure, ownership, or key file responsibilities changed
4. `vault/SESSION_LEDGER.md` with a short session note

Do not leave important project state only in chat.

## Vault Language Rule

All content written inside `vault/` must be in English.

- New entries: English only
- Rewritten active docs: English only
- Temporary notes, handoffs, and changelog entries: English only

## Vault Map

| Path | Purpose |
|------|---------|
| `vault/master_index.md` | Main knowledge base entry point |
| `vault/WORKFLOW.md` | Mandatory vault operating rules |
| `vault/SESSION_LEDGER.md` | Short per-session log |
| `vault/sprint.md` | Current sprint state and priorities |
| `vault/resume-plan.md` | Exact stop point and next action |
| `vault/CODE_MAP.md` | Code inventory and implementation notes |
| `vault/wiki/architecture/` | Vision, system design, auth, roadmap |
| `vault/wiki/services/` | Service contracts and runbooks |
| `vault/logs/changelog.md` | Change history and verification notes |

## Branches

- `main` — production
- `dev` — development
- `front/ayan` — current AYAN branch
- `front/taxi` — legacy

## Frontend

- Nuxt config: `frontend/nuxt.config.ts`
- App code: `frontend/app/`
- Service layers: `frontend/services/{ayan,agal,tal,uus}/`
- i18n: `frontend/i18n/locales/{ru,sah}.json`

## Service Layers

`nuxt.config.ts` extends:

- `services/ayan`
- `services/agal`
- `services/tal`
- `services/uus`

Each service lives under its own `app/` directory.

## Frontend Commands

```bash
npm run dev
npm run build
npm run build:static
npm run typecheck
npm run lint
npm run lint:fix
npm run format
npm run format:fix
```

## Core Workflow

1. Read the required vault files
2. Read service- or feature-specific vault docs
3. Implement the change
4. Verify the change
5. Update vault before closing the task

For VPS static frontend deploys, use `npm run build:static` instead of raw `npx nuxt build --preset github_pages`. This command forces safe same-origin `/api` and verifies generated HTML before deploy.

## Code Style

- ESLint + Prettier: `printWidth: 120`, `tabWidth: 4`, single quotes, no semicolons, `trailingComma: none`
- TypeScript is strict and verified with `npm run typecheck`
- Composables under `app/composables/` are auto-imported

## Nuxt Page Structure (Mandatory)

```text
services/ayan/app/pages/
├── ayan.vue
└── ayan/
    ├── index.vue
    ├── create.vue
    └── ...
```

- Parent wrapper must contain only `<NuxtPage />`
- Follow the same structure for every service

## Code Rules (Mandatory)

- Always use `navigateTo('/path')`, never `router.push()`
- Never use dynamic Tailwind interpolation like `bg-${color}-500/20`
- Guard Telegram SDK features with `supportsVersion('6.1')` before use
- Never build API URLs with `new URL(endpoint, base)`; use string concatenation
- Do not modify Telegram SDK source
- Do not create a `layers/` directory; use `services/`
- Use `useIntervalFn` from `@vueuse/core` for polling
- Extract duplicated shared UI into `services/ayan/app/components/`
- No hardcoded user-facing strings; use `t('key')`
- i18n keys must be nested, for example `ayan.order.status.open`

## Nuxt 4 + Nuxt UI Rules (Mandatory)

Before writing any page, component, or composable:

- prefer `useAsyncData` / `useFetch` over raw `$fetch` in setup
- use `useState` for shared state
- use `@nuxt/ui` components instead of raw HTML equivalents when possible
- style with Tailwind utilities
- use `defineNuxtPlugin` for plugins and `defineNuxtRouteMiddleware` for middleware
- rely on auto-imports for app composables/components/utils

## Design System

- Dark mode only
- Primary color: cyan `#5edac6`
- Background: `#0a0c0e`
- Font: Geist/Inter
- `@nuxt/ui` v4 with `colors: ['cyan', 'gray']`

## Environment

```bash
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

Mock/real API toggle lives in `frontend/app/config/api.config.ts`.
