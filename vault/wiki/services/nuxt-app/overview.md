# Nuxt App Overview

## Frontend Stack
- Nuxt 4 (Vue 3 Composition API)
- TypeScript (strict)
- @nuxt/ui v4 (Cyan theme, dark only)
- @nuxtjs/i18n (ru default + sah)

## Nuxt 4 Structure
```
frontend/
├── nuxt.config.ts          # Root config, extends service layers
├── app/                    # Base application
│   ├── pages/              # Hub + UI showcase
│   ├── components/         # Shared components (future)
│   ├── composables/        # useAuth, useTg, useAPI, usePolling
│   ├── types/              # api.ts — all type definitions
│   ├── layouts/            # default.vue
│   ├── utils/              # format.ts (future)
│   ├── middleware/         # auth.global.ts
│   ├── config/             # api.config.ts
│   └── assets/css/         # Design system
├── services/               # Service layers (each = Nuxt layer)
│   ├── ayan/               # Taxi — MVP (active)
│   ├── uus/                # Masters — stub
│   ├── agal/               # Delivery — stub
│   └── tal/                # Booking — stub
└── i18n/locales/           # ru.json, sah.json
```

## Key Conventions
- **Nuxt 4**: Все frontend файлы только в `frontend/app/`
- **Routing**: File-based через `pages/`
- **State**: `useState` + composables (no Pinia)
- **Navigation**: ALWAYS `navigateTo()` — never `router.push()`
- **Tailwind**: No dynamic class interpolation (`bg-${color}` — forbidden)
- **Auto-imports**: Composables auto-imported, no manual imports
- **No SSR**: `ssr: false`, SPA-only

## Composables Architecture (after rewrite)

| Composable | Purpose |
|------------|---------|
| `useAPI` | Base HTTP client ($fetch wrapper) |
| `useAuth` | Auth + auto-login + token persistence |
| `useTg` | Telegram WebApp SDK (fixed) |
| `useTaxiAPI` | Factory: returns ITaxiAPI implementation |
| `usePolling` | Smart polling with backoff |
| `useOrderStatus` | Status config map + helpers |
| `useTaxiOrder` | Order state management |

## API Integration
See: [[wiki/architecture/api-contract]]

## Routing
See: [[wiki/services/nuxt-app/routing]]

## Service Vision
See: [[wiki/architecture/ayan-vision]]
