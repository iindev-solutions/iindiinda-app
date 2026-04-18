# Nuxt App Routing — Ayan

## Page Structure (Nuxt 4 Layer)

```
frontend/services/ayan/app/pages/
├── ayan.vue                    → /ayan  (PARENT — <NuxtPage /> only)
└── ayan/
    ├── index.vue               → /ayan          (hub + role selection)
    ├── create.vue              → /ayan/create   (create order form)
    ├── my-order.vue            → /ayan/my-order (passenger: track order)
    ├── driver.vue              → /ayan/driver   (driver: dashboard)
    ├── orders.vue              → /ayan/orders   (driver: open orders)
    ├── active-ride.vue         → /ayan/active-ride (driver: active ride)
    └── complete.vue            → /ayan/complete (both: rating)
```

## Route Map

| Route | Component | Role | Purpose |
|-------|-----------|------|---------|
| `/` | `app/pages/index.vue` | Any | Service hub (4 services) |
| `/ayan` | `ayan/index.vue` | Any | Role selection + CTAs |
| `/ayan/create` | `ayan/create.vue` | Passenger | Create order |
| `/ayan/my-order` | `ayan/my-order.vue` | Passenger | Track active order |
| `/ayan/driver` | `ayan/driver.vue` | Driver | Dashboard + toggle online |
| `/ayan/orders` | `ayan/orders.vue` | Driver | Available orders list |
| `/ayan/active-ride` | `ayan/active-ride.vue` | Driver | Manage active ride |
| `/ayan/complete` | `ayan/complete.vue` | Both | Rating + summary |

## Navigation Rules

1. **ALWAYS** `navigateTo('/path')` — never `router.push()`
2. Back button: `useTg().onBackButtonClicked` → navigateTo(parent)
3. Layout header back: `<NuxtLink :to="parentPath">`

## Navigation Flow — Passenger

```
/ ──→ /ayan ──→ /ayan/create ──→ /ayan/my-order ──→ /ayan/complete
                ↑                   │    ↑               │
                └───────────────────┘    │               │
                               (cancel)  │               │
                                         │               ↓
                                         └──→ /ayan ←────┘
```

| From | To | Trigger |
|------|----|---------|
| / | /ayan | Click "Бардыбыт" |
| /ayan | /ayan/create | Click "Создать заказ" (passenger) |
| /ayan/create | /ayan/my-order | Order created successfully |
| /ayan/my-order | /ayan/complete | Status → completed |
| /ayan/my-order | /ayan | Order cancelled |
| /ayan/complete | /ayan/create | "Новая поездка" |
| /ayan/complete | / | "На главную" |

## Navigation Flow — Driver

```
/ ──→ /ayan ──→ /ayan/driver ──→ /ayan/orders ──→ /ayan/active-ride ──→ /ayan/complete
                ↑                  │    ↑               │    ↑               │
                │                  │    └───────────────┘    │               │
                │                  └────────────────────────┘               │
                └──────────────────────────────────────────────────────────┘
```

| From | To | Trigger |
|------|----|---------|
| / | /ayan | Click "Бардыбыт" |
| /ayan | /ayan/driver | Click "Водитель" (role) |
| /ayan/driver | /ayan/orders | Toggle online ON |
| /ayan/orders | /ayan/active-ride | Accept order |
| /ayan/active-ride | /ayan/complete | Status → completed |
| /ayan/active-ride | /ayan/orders | Passenger cancelled |
| /ayan/complete | /ayan/orders | "К заказам" |
| /ayan/complete | / | "На главную" |

## Middleware

### auth.global.ts
Runs on every route change:
1. Check `isAuthenticated`
2. If not → attempt `autoLogin()`
3. If autoLogin fails → show error toast (don't block navigation for MVP)

### Role Guard (future)
Pages can check role:
- `/ayan/create` requires `role === 'passenger'`
- `/ayan/orders` requires `role === 'driver'`
- `/ayan/driver` requires `role === 'driver'`

For MVP: no hard blocks, just UI-level checks (hide buttons for wrong role).

See also: [[wiki/services/nuxt-app/overview]], [[wiki/architecture/ayan-vision]]
